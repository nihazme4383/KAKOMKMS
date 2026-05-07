<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeamStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('team_students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_registration_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('matrix_no');
            $table->string('jersey_no')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('team_students');
    }
}
