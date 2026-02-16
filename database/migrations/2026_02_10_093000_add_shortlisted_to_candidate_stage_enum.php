<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE candidates MODIFY COLUMN stage ENUM('default', 'shortlisted', 'test_sent', 'test_received', '1st_interview', '2nd_interview', 'offer_sent', 'offer_accepted', 'joined', 'rejected') DEFAULT 'default'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE candidates MODIFY COLUMN stage ENUM('default', 'test_sent', 'test_received', '1st_interview', '2nd_interview', 'offer_sent', 'offer_accepted', 'joined', 'rejected') DEFAULT 'default'");
    }
};
