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
        Schema::table('candidates', function (Blueprint $table) {
            // Composite index for the main recruitment views (Filtering + Sorting)
            // Filters: department_id, designation_id, is_archived (from where clauses)
            // Sorting: created_at (from latest())
            $table->index(['designation_id', 'is_archived', 'created_at'], 'candidates_recruitment_index');
            
            // Single column indexes for common lookups and analytics
            $table->index('stage');
            $table->index('email');
            $table->index('finalized_at');
        });

        Schema::table('designations', function (Blueprint $table) {
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            $table->dropIndex('candidates_recruitment_index');
            $table->dropIndex(['stage']);
            $table->dropIndex(['email']);
            $table->dropIndex(['finalized_at']);
        });

        Schema::table('designations', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
        });
    }
};
