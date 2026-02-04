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
        Schema::create('reject_types', function (Blueprint $table) {
            //Primary key
            $table->increments('id');
            
            //Foreign keys
            $table->unsignedInteger('plant_id')->index();
            $table->unsignedInteger('reject_group_id')->index();

            //Data
            $table->string('name');
            $table->string('tag')->nullable();
            $table->tinyInteger('enabled')->index();
            $table->tinyInteger('locked')->default(0)->index();

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
        Schema::dropIfExists('reject_types');
    }
};


