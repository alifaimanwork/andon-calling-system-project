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
        Schema::create('work_centers', function (Blueprint $table) {
            //Primary key
            $table->increments('id');

            //Foreign keys
            $table->unsignedInteger('plant_id')->nullable()->index();
            
            $table->unsignedInteger('factory_id')->nullable()->index();
            $table->unsignedInteger('dashboard_layout_id')->nullable()->index();
            $table->unsignedInteger('current_production_id')->nullable()->index();
            $table->unsignedInteger('break_schedule_id')->nullable()->index();

            //Data
            $table->string('uid')->index();
            $table->string('name');
            $table->string('production_line_count');
            $table->tinyInteger('status')->unsigned()->default(0); // (0: IDLE, 1: DIE_CHANGE, 2: FIRST_CONFIRMATION, 3: RUNNING)
            $table->tinyInteger('downtime_state')->default(0); // 0: none, -ve: Unplanned downtime, +ve: Planned downtime

            $table->tinyInteger('enabled')->unsigned()->default(1); // 0: Disabled, 1: Enabled

            //percentage oee, availibility, performance, quality
            $table->float('threshold_oee');
            $table->float('threshold_availability');
            $table->float('threshold_performance');
            $table->float('threshold_quality');

            //-SAP Paths
            $table->string('pps_source');
            $table->string('gr_ok_destination');
            $table->string('gr_ng_destination');
            $table->string('gr_qi_destination');
            $table->string('rw_ok_destination');
            $table->string('rw_ng_destination');
            $table->string('ett10_destination');
            $table->string('ett20_destination');

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
        Schema::dropIfExists('work_centers');
    }
};
