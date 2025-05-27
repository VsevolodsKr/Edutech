<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\StringType;

return new class extends Migration
{
    public function up()
    {
        // First, modify the Contents table to ensure proper foreign key types
        Schema::table('Contents', function (Blueprint $table) {
            $table->unsignedBigInteger('teacher_id')->change();
            $table->unsignedBigInteger('playlist_id')->change();
        });

        // Add foreign keys with CASCADE
        Schema::table('Playlists', function (Blueprint $table) {
            // Drop if exists and recreate
            if (Schema::hasColumn('Playlists', 'teacher_id')) {
                $table->foreign('teacher_id')
                      ->references('id')
                      ->on('Teachers')
                      ->onDelete('cascade');
            }
        });

        Schema::table('Contents', function (Blueprint $table) {
            if (Schema::hasColumn('Contents', 'teacher_id')) {
                $table->foreign('teacher_id')
                      ->references('id')
                      ->on('Teachers')
                      ->onDelete('cascade');
            }

            if (Schema::hasColumn('Contents', 'playlist_id')) {
                $table->foreign('playlist_id')
                      ->references('id')
                      ->on('Playlists')
                      ->onDelete('cascade');
            }
        });

        Schema::table('Comments', function (Blueprint $table) {
            if (Schema::hasColumn('Comments', 'teacher_id')) {
                $table->foreign('teacher_id')
                      ->references('id')
                      ->on('Teachers')
                      ->onDelete('cascade');
            }
        });

        Schema::table('Likes', function (Blueprint $table) {
            if (Schema::hasColumn('Likes', 'teacher_id')) {
                $table->foreign('teacher_id')
                      ->references('id')
                      ->on('Teachers')
                      ->onDelete('cascade');
            }
        });
    }

    public function down()
    {
        // Remove CASCADE constraints if they exist
        Schema::table('Playlists', function (Blueprint $table) {
            if (Schema::hasColumn('Playlists', 'teacher_id')) {
                $table->dropForeign(['teacher_id']);
            }
        });

        Schema::table('Contents', function (Blueprint $table) {
            if (Schema::hasColumn('Contents', 'teacher_id')) {
                $table->dropForeign(['teacher_id']);
            }
            if (Schema::hasColumn('Contents', 'playlist_id')) {
                $table->dropForeign(['playlist_id']);
            }
            // Convert back to string type as it was originally
            $table->string('teacher_id')->change();
            $table->string('playlist_id')->change();
        });

        Schema::table('Comments', function (Blueprint $table) {
            if (Schema::hasColumn('Comments', 'teacher_id')) {
                $table->dropForeign(['teacher_id']);
            }
        });

        Schema::table('Likes', function (Blueprint $table) {
            if (Schema::hasColumn('Likes', 'teacher_id')) {
                $table->dropForeign(['teacher_id']);
            }
        });
    }
}; 