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
        Schema::create('production_orders', function (Blueprint $table) {
            //Primary key
            $table->increments('id');
            
            //Foreign keys
            $table->unsignedInteger('plant_id')->index();
            $table->unsignedInteger('part_id')->index()->nullable();
            $table->unsignedInteger('work_center_id')->index();
            
            //Data
            $table->string('order_no');
            $table->integer('plan_quantity');
            $table->string('unit_of_measurement');
            $table->tinyInteger('status')->index()->default(0); //0: not started yet, 1: production in progress, 2: production closed / completed

            //-Production Data
            $table->integer('actual_output')->default(0);
            $table->integer('pending_count')->default(0);
            $table->integer('ok_count')->default(0);
            $table->integer('ng_count')->default(0);

            //-PPS Data
            $table->integer('pps_seq')->index();
            $table->string('pps_plant');
            $table->string('pps_factory');
            $table->string('pps_line');
            $table->string('pps_part_no')->index();
            $table->string('pps_part_name');
            $table->string('pps_shift');

            //-PPS File Info
            $table->string('pps_status');
            $table->string('pps_filename');
            $table->string('pps_filehash');

            
            //Timestamp
            $table->timestamp('plan_start')->nullable();
            $table->timestamp('plan_finish')->nullable();
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
        Schema::dropIfExists('production_orders');
    }
};
