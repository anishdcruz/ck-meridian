<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentsMadeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments_made', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('vendor_id')->unsigned();
            $table->integer('purchase_order_id')->unsigned();
            $table->string('number')->unique();
            $table->date('payment_date');
            $table->string('method');
            $table->text('note')->nullable();
            $table->double('amount_paid', 15, 4);
            $table->double('transaction_fees', 15, 4);
            $table->double('net_amount', 15, 4);
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
        Schema::dropIfExists('payments_made');
    }
}
