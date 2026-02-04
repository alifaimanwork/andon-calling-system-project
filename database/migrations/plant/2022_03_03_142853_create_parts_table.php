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
        Schema::create('parts', function (Blueprint $table) {
            //Primary key
            $table->increments('id');

            //Foreign keys
            $table->unsignedInteger('plant_id')->index()->nullable();
            $table->unsignedInteger('work_center_id')->index()->nullable();            

            //Data
            $table->string('part_no')->index()->nullable();
            $table->unsignedInteger('line_no')->index();
            $table->string('name');
            $table->integer('setup_time');
            $table->integer('cycle_time');
            $table->integer('packaging');
            $table->float('reject_target');
            $table->string('side');
            $table->unsignedInteger('opc_part_id')->index();

            $table->tinyInteger('enabled')->index();

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
        Schema::dropIfExists('parts');
    }
};
