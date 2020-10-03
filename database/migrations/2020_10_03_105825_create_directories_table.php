<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDirectoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('directories', function (Blueprint $table) {
            $table->id();
            $table->nestedSet();
            $table->bigInteger('owner_id')->index()->nullable();
            $table->string('name')->index();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table
                ->bigInteger('current_directory_id')
                ->after('active_project_id')
                ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('directories');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('current_directory_id');
        });
    }
}
