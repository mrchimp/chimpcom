<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateChimpcomTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aliases', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('alias');
            $table->nullableTimestamps();
        });

        Schema::create('cities', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 32);
            $table->string('country', 32);
            $table->string('city', 32);
            $table->string('accent_city', 32);
            $table->integer('region');
            $table->integer('population');
            $table->float('lat');
            $table->float('lon');
        });

        Schema::create('feeds', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('name', 32);
            $table->string('url', 255);
            $table->integer('type');
            $table->nullableTimestamps();
        });

        Schema::create('mans', function (Blueprint $table) {
            $table->increments('id');
            $table->string('command', 32);
            $table->text('description');
            $table->text('example');
            $table->text('see_also');
            $table->text('usage');
            $table->text('aliases');
            $table->nullableTimestamps();
        });

        Schema::create('memories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 32);
            $table->string('content', 255);
            $table->integer('user_id');
            $table->boolean('public', 255);
            $table->nullableTimestamps();
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('message', 255);
            $table->double('recipient_id');
            $table->integer('author_id');
            $table->boolean('has_been_read');
            $table->nullableTimestamps();
        });

        Schema::create('oneliners', function (Blueprint $table) {
            $table->increments('id');
            $table->string('command', 16);
            $table->string('response', 255);
            $table->nullableTimestamps();
        });

        Schema::create('projects', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('is_new');
            $table->string('name', 255);
            $table->string('description', 255)->nullable();
            $table->integer('user_id');
            $table->nullableTimestamps();
        });

        Schema::create('shortcuts', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255);
            $table->string('url', 255);
            $table->nullableTimestamps();
        });

        Schema::create('tasks', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('project_id');
            $table->string('description', 255);
            $table->integer('user_id');
            $table->timestamp('time_completed')->nullable();
            $table->integer('priority');
            $table->boolean('completed');
            $table->nullableTimestamps();
        });

        // Schema::create('users' , function(Blueprint $table) {
        //     $table->increments('id');
        //     $table->rememberToken();
        //     $table->softDeletes();
        //     $table->string('email')->unique();
        //     $table->string('password', 64);
        //     $table->string('name');
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('aliases');
        Schema::dropIfExists('cities');
        Schema::dropIfExists('feeds');
        Schema::dropIfExists('mans');
        Schema::dropIfExists('memories');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('oneliners');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('shortcuts');
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('users');
    }
}
