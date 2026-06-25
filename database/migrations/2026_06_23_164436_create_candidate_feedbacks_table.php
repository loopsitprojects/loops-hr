<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('candidate_feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained('candidates')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('feedback');
            $table->timestamps();
        });

        // Migrate existing non-empty HOD comments
        $candidates = DB::table('candidates')->whereNotNull('hod_comment')->where('hod_comment', '!=', '')->get();
        
        if ($candidates->isNotEmpty()) {
            foreach ($candidates as $candidate) {
                // Try to find HOD in candidate's department
                $user = null;
                if ($candidate->department_id) {
                    $user = DB::table('users')
                        ->where('department_id', $candidate->department_id)
                        ->where('role', 'Head of Department')
                        ->first();
                }
                
                // If not found, find first Admin or HR Admin
                if (!$user) {
                    $user = DB::table('users')
                        ->whereIn('role', ['Super Admin', 'HR Admin'])
                        ->first();
                }
                
                // If still not found, find the very first user in the DB
                if (!$user) {
                    $user = DB::table('users')->first();
                }

                if ($user) {
                    DB::table('candidate_feedbacks')->insert([
                        'candidate_id' => $candidate->id,
                        'user_id' => $user->id,
                        'feedback' => $candidate->hod_comment,
                        'created_at' => $candidate->updated_at ?? now(),
                        'updated_at' => $candidate->updated_at ?? now(),
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidate_feedbacks');
    }
};
