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
        Schema::create('plant_user', function (Blueprint $table) {
            //Primary key
            $table->increments('id');
            
            //Foreign keys
            $table->unsignedInteger('plant_id')->index();
            $table->unsignedInteger('user_id')->index();

            //Data
            $table->tinyInteger('role')->unsigned(); //0: default, 1: administrator
            $table->tinyInteger('web_permission')->unsigned(); //0: false, 1: true
            $table->tinyInteger('terminal_permission')->unsigned(); //0 false, 1 true

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
        Schema::dropIfExists('plant_user');
    }
};


