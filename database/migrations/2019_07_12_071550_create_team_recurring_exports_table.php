<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeamRecurringExportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('team_recurring_exports', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('team_id');
            $table->integer('export_id');
            $table->string('frequency');
            $table->string('send_on')->nullable();
            $table->string('send_at');
            $table->date('last_generated_date')->nullable();
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
        Schema::dropIfExists('team_recurring_exports');
    }
}
