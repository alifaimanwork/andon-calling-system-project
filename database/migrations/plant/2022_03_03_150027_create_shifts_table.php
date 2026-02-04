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
        Schema::create('shifts', function (Blueprint $table) {
            //Primary key
            $table->increments('id');

            //Foreign keys
            $table->unsignedInteger('plant_id')->index();
            $table->unsignedInteger('shift_type_id')->index();

            //Data
            $table->tinyInteger('day_of_week')->unsigned(); //ISO-8601: 1: Monday ... 7:Sunday
            $table->time('start_time');

            $table->unsignedInteger('duration');
            $table->unsignedInteger('normal_duration');

            $table->tinyInteger('enabled')->default(1)->index();

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
        Schema::dropIfExists('shifts');
    }
};
