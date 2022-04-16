<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Facades\TeamConfig;

class CreateQuotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('contact_id')->unsigned();
            $table->string('number')->unique();
            $table->date('issue_date');
            $table->date('expiry_date');
            $table->integer('expiry_in_days');
            $table->string('reference')->nullable();
            $table->double('sub_total', 15, 4);
            $table->double('discount', 15, 4)->default(0);
            $table->double('tax')->default(0);
            $table->double('tax_total', 15, 4)->default(0);
            $table->double('tax_2')->default(0);
            $table->double('tax_2_total', 15, 4)->default(0);
            $table->double('grand_total', 15, 4);
            $table->integer('term_id')->unsigned();
            $table->string('status_id');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('quotation_lines', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('quotation_id')->unsigned();
            $table->integer('item_id')->unsigned();
            $table->float('qty');
            $table->double('unit_price');
            $table->timestamps();
        });

        Schema::create('quotation_statuses', function (Blueprint $table) {
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

        foreach(config('team.quotation_statuses') as $c) {
            $id = DB::connection('team')->table('quotation_statuses')
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
        Schema::dropIfExists('quotations');
        Schema::dropIfExists('quotation_lines');
        Schema::dropIfExists('quotation_statuses');
    }
}
