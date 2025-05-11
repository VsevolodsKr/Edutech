<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop the role column
            $table->dropColumn('role');
            
            // Add the status column
            $table->enum('status', ['aktīvs', 'neaktīvs'])->default('aktīvs')->after('password');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop the status column
            $table->dropColumn('status');
            
            // Add back the role column
            $table->enum('role', ['admin', 'teacher', 'student'])->default('student')->after('password');
        });
    }
}; 