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
        Schema::create('Bookmarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->reference('id')->on('Users');
            $table->foreignId('playlist_id')->reference('id')->on('Playlists');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Bookmarks');
    }
};
