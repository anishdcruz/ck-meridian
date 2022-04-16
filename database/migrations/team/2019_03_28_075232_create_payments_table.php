<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('contact_id')->unsigned();
            $table->integer('invoice_id')->unsigned();
            $table->string('number')->unique();
            $table->date('payment_date');
            $table->string('charge_id')->nullable();
            $table->string('status')->nullable();
            $table->string('method');
            $table->text('note')->nullable();
            $table->double('amount_received', 15, 4);
            $table->double('transaction_fees', 15, 4);
            $table->double('net_amount', 15, 4);
            $table->double('amount_refunded', 15, 4)->default(0);
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
        Schema::dropIfExists('payments');
    }
}
