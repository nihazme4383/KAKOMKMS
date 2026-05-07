<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeamOfficialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('team_officials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_registration_id')->constrained()->cascadeOnDelete();
            $table->enum('role', ['coach_1', 'coach_2', 'manager']);
            $table->string('name')->nullable();
            $table->string('position')->nullable();
            $table->string('phone_no')->nullable();
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
        Schema::dropIfExists('team_officials');
    }
}
