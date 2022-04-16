<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserMetricsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('user_metrics', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('filter_id');
            $table->string('card_label');
            $table->string('metric_type');
            $table->string('value_type')->nullable();
            $table->string('value_field')->nullable();
            $table->string('chart_type')->nullable();
            $table->string('color')->nullable();
            $table->string('i')->nullable();
            $table->integer('x')->nullable();
            $table->integer('y')->nullable();
            $table->integer('w')->nullable();
            $table->integer('h')->nullable();
            $table->string('y_axis_type')->nullable();
            $table->string('y_axis_field')->nullable();
            $table->string('x_axis_type')->nullable();
            $table->string('x_axis_field')->nullable();
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
        Schema::dropIfExists('user_metrics');
    }
}
