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
        // First, modify the column type
        DB::statement("ALTER TABLE contact MODIFY COLUMN status ENUM('jauns', 'atvērts', 'apstrāde', 'pabeigts') NOT NULL DEFAULT 'jauns'");
        
        // Update any existing records to use the new default value
        DB::table('contact')->whereNull('status')->orWhere('status', '')->update(['status' => 'jauns']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to the previous state if needed
        DB::statement("ALTER TABLE contact MODIFY COLUMN status VARCHAR(255) NULL");
    }
};
