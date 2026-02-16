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
        DB::statement("ALTER TABLE candidates MODIFY COLUMN stage ENUM('test', '1st_interview', '2nd_interview', 'offer_sent', 'offer_accepted', 'joined', 'rejected') DEFAULT 'test'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Warning: This might cause data loss for 'rejected' candidates if rolled back
        DB::statement("ALTER TABLE candidates MODIFY COLUMN stage ENUM('test', '1st_interview', '2nd_interview', 'offer_sent', 'offer_accepted', 'joined') DEFAULT 'test'");
    }
};
