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
        if (!Schema::hasColumn('teachers', 'status')) {
            Schema::table('teachers', function (Blueprint $table) {
                $table->enum('status', ['aktīvs', 'neaktīvs'])->default('aktīvs')->after('image');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('teachers', 'status')) {
            Schema::table('teachers', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};
