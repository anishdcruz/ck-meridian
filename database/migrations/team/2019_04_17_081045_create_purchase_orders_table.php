<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Facades\TeamConfig;

class CreatePurchaseOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('vendor_id')->unsigned();
            $table->string('number')->unique();
            $table->date('issue_date');
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
            $table->integer('foreign_currency_id')->nullable();
            $table->float('exchange_rate')->nullable();
            $table->timestamps();
        });

        Schema::create('purchase_order_lines', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('purchase_order_id')->unsigned();
            $table->integer('item_id')->unsigned();
            $table->float('qty');
            $table->double('unit_price');
            $table->timestamps();
        });

        Schema::create('purchase_order_statuses', function (Blueprint $table) {
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

        foreach(config('team.purchase_order_statuses') as $c) {
            $id = DB::connection('team')->table('purchase_order_statuses')
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
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('purchase_order_lines');
        Schema::dropIfExists('purchase_order_statuses');
    }
}
