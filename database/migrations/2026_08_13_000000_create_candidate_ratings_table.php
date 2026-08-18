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
        Schema::create('candidate_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained('candidates')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('interview_id')->nullable()->constrained('interviews')->nullOnDelete();
            $table->decimal('overall_score', 3, 1)->default(0.0);
            $table->string('recommendation'); // strong_no, no, yes, strong_yes
            $table->json('area_ratings')->nullable(); // [{"area": "Communication", "score": 5}, ...]
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['candidate_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidate_ratings');
    }
};
