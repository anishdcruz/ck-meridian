<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeamRecurringInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('team_recurring_invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('team_id');
            $table->integer('recurring_id');
            $table->string('frequency');
            $table->string('send_on')->nullable();
            $table->string('send_at');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('never_expiry')->default(1);
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
        Schema::dropIfExists('team_recurring_invoices');
    }
}
