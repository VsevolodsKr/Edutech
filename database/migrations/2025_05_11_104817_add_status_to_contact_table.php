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
        if (!Schema::hasColumn('contact', 'status')) {
            Schema::table('contact', function (Blueprint $table) {
                $table->enum('status', ['jauns', 'atvērts', 'apstrāde', 'pabeigts'])->default('jauns')->after('message');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('contact', 'status')) {
            Schema::table('contact', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};
