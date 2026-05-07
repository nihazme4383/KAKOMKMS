<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJerseyNoAwayToTeamStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('team_students', function (Blueprint $table) {
            $table->string('jersey_no_away')->nullable()->after('jersey_no');
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
            $table->dropColumn('jersey_no_away');
        });
    }
}
