<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Facades\TeamConfig;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('contact_id')->unsigned();
            $table->string('number')->unique();
            $table->date('issue_date');
            $table->date('due_date');
            $table->integer('due_in_days');
            $table->integer('quotation_id')->nullable();
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
            $table->string('payment_code')->unique();
            $table->string('status_id');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('invoice_lines', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('invoice_id')->unsigned();
            $table->integer('item_id')->unsigned();
            $table->float('qty');
            $table->double('unit_price');
            $table->timestamps();
        });

        Schema::create('invoice_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('color')->unique();
            $table->boolean('locked')->default(0);
            $table->timestamps();
        });

        $this->seed();
    }

    protected function seed()
    {

        foreach(config('team.invoice_statuses') as $c) {
            $id = DB::connection('team')->table('invoice_statuses')
                ->insertGetId([
                    'name' => $c['name'],
                    'color' => $c['color'],
                    'locked' => isset($c['locked']) ? $c['locked'] : 0,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            if(isset($c['default'])) {
                TeamConfig::set($c['default'], $id);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('invoice_lines');
        Schema::dropIfExists('invoice_statuses');
    }
}
