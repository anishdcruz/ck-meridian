<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeamConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('team_configs', function (Blueprint $table) {
            $table->string('key')->unique();
            $table->text('value')->nullable();
        });

        $this->seed();
    }

    protected function seed()
    {
        $cs = config('team.defaults');

        foreach($cs as $key => $value) {
            DB::connection('team')->table('team_configs')->insert([
                'key' => $key,
                'value' => $value
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
        Schema::dropIfExists('team_configs');
    }
}
