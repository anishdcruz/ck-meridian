<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('category_id')->nullable();
            $table->string('number')->unique();
            $table->string('name');
            $table->string('organization')->nullable();
            $table->string('tax_id')->nullable();
            $table->string('title')->nullable();
            $table->string('department')->nullable();
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->string('fax')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->text('primary_address')->nullable();
            $table->text('other_address')->nullable();
            $table->double('total_revenue', 15, 4)->default(0);
            // $table->double('total_amount_refunded', 15, 4)->default(0);
            $table->double('amount_receivable', 15, 4)->default(0);
            $table->timestamps();
        });

        Schema::create('contact_categories', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->timestamps();
        });

        $this->seed();
    }

    protected function seed()
    {
        $cs = config('team.contact_categories');

        foreach($cs as $name) {
            DB::connection('team')->table('contact_categories')->insert([
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
        Schema::dropIfExists('contacts');
        Schema::dropIfExists('contact_categories');
    }
}
