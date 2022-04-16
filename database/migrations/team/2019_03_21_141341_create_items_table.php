<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->unique();
            $table->text('description');
            $table->integer('category_id')->unsigned();
            $table->integer('uom_id')->unsigned();
            $table->double('unit_price', 15, 4)->default(0);
            $table->timestamps();
        });

        Schema::create('item_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('item_uoms', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->timestamps();
        });

        $this->seed();
    }

    protected function seed()
    {
        $cs = config('team.item_categories');

        foreach($cs as $name) {
            DB::connection('team')->table('item_categories')->insert([
                'name' => $name,
                'created_at' => now()
            ]);
        }

        $cs = config('team.item_uoms');

        foreach($cs as $name) {
            DB::connection('team')->table('item_uoms')->insert([
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
        Schema::dropIfExists('items');
        Schema::dropIfExists('item_categories');
        Schema::dropIfExists('item_uoms');
    }
}
