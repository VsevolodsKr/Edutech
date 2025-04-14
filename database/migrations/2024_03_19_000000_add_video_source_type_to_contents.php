<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('Contents', function (Blueprint $table) {
            $table->enum('video_source_type', ['file', 'youtube'])->default('file')->after('description');
        });
    }

    public function down()
    {
        Schema::table('Contents', function (Blueprint $table) {
            $table->dropColumn('video_source_type');
        });
    }
}; 