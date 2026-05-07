<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIcNoToTeamStudentsAndOfficialsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('team_students', function (Blueprint $table) {
            $table->string('ic_no')->nullable()->after('matrix_no');
        });

        Schema::table('team_officials', function (Blueprint $table) {
            $table->string('ic_no')->nullable()->after('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('team_students', function (Blueprint $table) {
            $table->dropColumn('ic_no');
        });

        Schema::table('team_officials', function (Blueprint $table) {
            $table->dropColumn('ic_no');
        });
    }
}
