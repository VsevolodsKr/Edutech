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
        Schema::create('Contents', function (Blueprint $table) {
            $table->id();
            $table->string('teacher_id');
            $table->string('playlist_id');
            $table->string('title');
            $table->text('description');
            $table->string('video');
            $table->string('thumb');
            $table->date('date');
            $table->enum('status', ['active', 'deactive']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Contents');
    }
}; 