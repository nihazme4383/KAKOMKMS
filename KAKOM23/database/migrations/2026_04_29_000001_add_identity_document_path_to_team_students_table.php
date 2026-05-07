<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdentityDocumentPathToTeamStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('team_students', function (Blueprint $table) {
            $table->string('identity_document_path')->nullable()->after('jersey_no');
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
            $table->dropColumn('identity_document_path');
        });
    }
}
