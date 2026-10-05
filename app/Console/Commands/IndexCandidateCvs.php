<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Candidate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Smalot\PdfParser\Parser;

class IndexCandidateCvs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'candidates:index-cvs 
                            {--force : Re-index candidates even if they already have parsed_content}
                            {--limit= : Maximum number of candidates to process}
                            {--id= : Specific candidate ID to index}
                            {--min-id= : Minimum candidate ID to process}
                            {--max-id= : Maximum candidate ID to process}
                            {--designation= : Only index candidates for a specific designation ID}
                            {--active-only : Only index active (non-archived) candidates}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extract and index text content from candidate CVs for skill-based keyword searching';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        ini_set('memory_limit', '-1');
        $this->info('Starting CV text indexing for candidate skill search...');

        $query = Candidate::query()->whereNotNull('cv_path')->where('cv_path', '!=', '');

        if ($this->option('id')) {
            $query->where('id', $this->option('id'));
        } elseif (!$this->option('force')) {
            $query->where(function ($q) {
                $q->whereNull('parsed_content')
                  ->orWhere('parsed_content', '');
            });
        }

        if ($this->option('min-id')) {
            $query->where('id', '>=', (int) $this->option('min-id'));
        }

        if ($this->option('max-id')) {
            $query->where('id', '<=', (int) $this->option('max-id'));
        }

        if ($this->option('designation')) {
            $query->where('designation_id', $this->option('designation'));
        }

        if ($this->option('active-only')) {
            $query->where('is_archived', 0);
        }

        // Always prioritize active candidates first, then newest candidates
        $query->orderBy('is_archived', 'asc')->orderBy('id', 'desc');

        if ($this->option('limit')) {
            $query->limit((int) $this->option('limit'));
        }

        $candidates = $query->get();
        $total = $candidates->count();

        if ($total === 0) {
            $this->info('No candidates require CV indexing.');
            return 0;
        }

        $this->info("Found {$total} candidate(s) to process.");
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $parser = new Parser();
        $disk = Storage::disk('ftp_cvs');

        $success = 0;
        $failed = 0;
        $skipped = 0;

        foreach ($candidates as $candidate) {
            $path = ltrim($candidate->cv_path, '/');
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

            // Only support PDF and DOCX files
            if ($ext !== 'pdf' && $ext !== 'docx') {
                $candidate->update(['parsed_content' => '[NON_PDF_DOC]']);
                $skipped++;
                $bar->advance();
                continue;
            }

            try {
                $fileContent = @$disk->get($path);
                if (empty($fileContent)) {
                    $candidate->update(['parsed_content' => '[FILE_NOT_FOUND]']);
                    $skipped++;
                    $bar->advance();
                    continue;
                }

                $text = '';
                if ($ext === 'pdf') {
                    $pdf = $parser->parseContent($fileContent);
                    $text = trim($pdf->getText());
                } elseif ($ext === 'docx') {
                    $tmpPath = tempnam(sys_get_temp_dir(), 'cv_docx_');
                    file_put_contents($tmpPath, $fileContent);
                    $zip = new \ZipArchive();
                    if ($zip->open($tmpPath) === true) {
                        $xml = $zip->getFromName('word/document.xml');
                        if ($xml) {
                            $text = trim(strip_tags($xml));
                        }
                        $zip->close();
                    }
                    @unlink($tmpPath);
                }

                if (!empty($text)) {
                    $candidate->update(['parsed_content' => $text]);
                    $success++;
                } else {
                    // Mark as processed so it is not re-attempted endlessly
                    $candidate->update(['parsed_content' => '[NO_EXTRACTABLE_TEXT]']);
                    $skipped++;
                }
            } catch (\Throwable $e) {
                $failed++;
                try {
                    $candidate->update(['parsed_content' => '[INDEX_ERROR]']);
                } catch (\Throwable $ignored) {}
                Log::warning("Failed to index CV for candidate #{$candidate->id} ({$candidate->name}): " . $e->getMessage());
            } finally {
                unset($fileContent, $pdf, $text);
                if ($bar->getProgress() % 10 === 0) {
                    gc_collect_cycles();
                }
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Candidates Checked', $total],
                ['Successfully Indexed', $success],
                ['Skipped (File missing / empty text)', $skipped],
                ['Errors', $failed],
            ]
        );

        $this->info('CV indexing completed successfully!');
        return 0;
    }
}
