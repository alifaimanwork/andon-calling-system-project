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
        Schema::create('user_work_center', function (Blueprint $table) {
            //Primary key
            $table->increments('id');

            //Foreign keys
            $table->unsignedInteger('user_id')->index();
            $table->unsignedInteger('work_center_id')->index();

            //Data
            $table->unsignedInteger('terminal_permission'); //0: none, 1: operation, 2: rework, 3: both

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
        Schema::dropIfExists('user_work_center');
    }
};


