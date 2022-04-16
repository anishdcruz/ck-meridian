<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('vendor_id')->nullable();
            $table->integer('expense_id')->unsigned();
            $table->integer('category_id')->unsigned();
            $table->string('number')->unique();
            $table->date('payment_date');
            $table->string('method');
            $table->text('note')->nullable();
            $table->double('sub_total', 15, 4);
            $table->double('tax')->default(0);
            $table->double('tax_total', 15, 4)->default(0);
            $table->double('tax_2')->default(0);
            $table->double('tax_2_total', 15, 4)->default(0);
            $table->double('grand_total', 15, 4);
            $table->string('reference');
            $table->timestamps();
        });

        Schema::create('expense_categories', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->timestamps();
        });

        $this->seed();
    }

    protected function seed()
    {
        $cs = config('team.expense_categories');

        foreach($cs as $name) {
            DB::connection('team')->table('expense_categories')->insert([
                'name' => $name,
                'created_at' => now()
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('expense_categories');
    }
}
