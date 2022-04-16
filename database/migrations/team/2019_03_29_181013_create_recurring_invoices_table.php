<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecurringInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recurring_invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->string('frequency');
            $table->integer('send_on')->nullable();
            $table->string('send_at');
            $table->string('title');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->integer('due_after');
            $table->boolean('never_expiry')->default(1);
            $table->date('last_generated_date')->nullable();
            $table->integer('contact_id')->unsigned();
            $table->string('reference')->nullable();
            $table->double('sub_total', 15, 4);
            $table->double('discount', 15, 4)->default(0);
            $table->double('tax')->default(0);
            $table->double('tax_total', 15, 4)->default(0);
            $table->double('tax_2')->default(0);
            $table->double('tax_2_total', 15, 4)->default(0);
            $table->double('grand_total', 15, 4);
            $table->double('amount_paid', 15, 4)->default(0);
            $table->timestamp('paid_at')->nullable();
            $table->integer('term_id')->unsigned();
            $table->string('status_id');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('recurring_invoice_list', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('tenant_id');
            $table->integer('recurring_id');
            $table->string('frequency');
            $table->string('send_on')->nullable();
            $table->string('send_at');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('never_expiry')->default(1);
            $table->timestamp('last_generated_date')->nullable();
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
        Schema::dropIfExists('recurring_invoices');
    }
}
