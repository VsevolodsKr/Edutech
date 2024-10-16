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
        Schema::create('Teachers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('profession', 50);
            $table->string('email', 50)->unique();
            $table->string('password', 100);
            $table->string('image', 100);
            $table->rememberToken();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Teachers');
    }
};
