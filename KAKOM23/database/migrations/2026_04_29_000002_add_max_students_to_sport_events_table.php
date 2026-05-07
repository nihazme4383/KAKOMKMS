<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMaxStudentsToSportEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sport_events', function (Blueprint $table) {
            $table->unsignedTinyInteger('max_students')->default(25)->after('requires_jersey_no');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sport_events', function (Blueprint $table) {
            $table->dropColumn('max_students');
        });
    }
}
