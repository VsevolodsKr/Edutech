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
        Schema::dropIfExists('Likes');
        Schema::create('Likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->reference('id')->on('Users');
            $table->foreignId('teacher_id')->reference('id')->on('Teachers');
            $table->foreignId('content_id')->reference('id')->on('Contents');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Likes');
    }
};
