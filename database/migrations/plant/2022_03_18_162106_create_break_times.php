<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('break_times', function (Blueprint $table) {
            //Primary key
            $table->increments('id');

            //Foreign keys
            $table->unsignedInteger('break_schedule_id')->index();
        
            $table->time('start_time');
            $table->integer('duration');
            $table->tinyInteger('day_of_week');

            //Timestamp
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
        Schema::dropIfExists('break_times');
    }
};
