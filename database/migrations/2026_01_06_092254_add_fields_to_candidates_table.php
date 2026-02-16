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
            $table->string('designation')->nullable()->after('phone');
            $table->enum('stage', [
                'test',
                '1st_interview',
                '2nd_interview',
                'offer_sent',
                'offer_accepted',
                'joined'
            ])->default('test')->after('status');
            $table->text('hod_comment')->nullable()->after('stage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            $table->dropColumn(['designation', 'stage', 'hod_comment']);
        });
    }
};
