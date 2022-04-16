<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('owner_id')->index()->nullable();
            $table->string('name');
            $table->string('database')->unique();
            $table->string('uuid')->unique();

            $table->string('currency_id');
            $table->string('lang_id');
            $table->string('timezone');
            $table->string('date_format');

            $table->boolean('is_setup')->default(0);
            $table->string('logo_file')->nullable();
            $table->string('stripe_user_id')->nullable();
            $table->boolean('livemode')->default(0);
            $table->timestamp('stripe_connected_at')->nullable();
            $table->timestamp('initialized_at')->nullable();
            $table->timestamps();
        });

        Schema::create('user_teams', function (Blueprint $table) {
            $table->integer('team_id');
            $table->integer('user_id');
            $table->string('type', 25);
            $table->integer('role_id');
            $table->unique(['team_id', 'user_id']);
        });

        Schema::create('invitations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('team_id')->index();
            $table->integer('role_id')->index();
            $table->integer('user_id')->nullable()->index();
            $table->string('email');
            $table->string('token', 40)->unique();
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
        Schema::dropIfExists('teams');
        Schema::drop('user_teams');
        Schema::drop('invitations');
    }
}
