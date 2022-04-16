<?php

use Illuminate\Database\Migrations\Migration;

class CreateCurrencyTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('currencies', function ($table) {
            $table->increments('id')->unsigned();
            $table->string('name');
            $table->char('code', 3)->index();
            $table->integer('precision');
            $table->integer('subunit');
            $table->string('symbol')->nullable();
            $table->boolean('symbol_first');
            $table->char('decimal_mark')->nullable();
            $table->char('thousands_separator')->nullable();
            $table->timestamps();
        });

        $cs = config('currency');
        $now = now();
        foreach($cs as $key => $value) {

            DB::table('currencies')->insert(array_merge(
                $value,
                ['code' => $key, 'created_at' => $now, 'updated_at' => $now]
            ));
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('currencies');
    }
}
