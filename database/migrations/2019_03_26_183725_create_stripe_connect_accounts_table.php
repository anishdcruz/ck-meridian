<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStripeConnectAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stripe_connect_accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('team_id')->index();
            $table->string('country');
            $table->string('default_currency');
            $table->string('email')->nullable();
            $table->boolean('payouts_enabled');
            $table->boolean('charges_enabled');
            $table->boolean('details_submitted');
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
        Schema::dropIfExists('stripe_connect_accounts');
    }
}
