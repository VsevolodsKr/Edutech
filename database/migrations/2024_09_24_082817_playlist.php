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
        Schema::create('Playlist', function (Blueprint $table) {
            $table->id();
            $table->string('teacher_id');
            $table->string('title', 100);
            $table->text('description');
            $table->string('thumb', 100);
            $table->date('date');
            $table->string('status', 20);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Playlist');
    }
};