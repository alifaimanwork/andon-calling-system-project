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
        Schema::create('dashboard_layouts', function (Blueprint $table) {
            //Primary key
            $table->increments('id');

            //Data
            $table->string('name');
            $table->integer('capacity');
            $table->string('preview_image');
            $table->mediumText('layout_data');

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
        Schema::dropIfExists('dashboard_layouts');
    }
};


