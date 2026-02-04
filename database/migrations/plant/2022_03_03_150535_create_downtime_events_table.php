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
        Schema::create('downtime_events', function (Blueprint $table) {
            //Primary key
            $table->id();

            //Foreign keys
            $table->unsignedInteger('production_id')->index();
            $table->unsignedInteger('downtime_id')->nullable()->index();
            $table->unsignedInteger('user_id')->nullable()->index();

            //Data
            /* Event type follow workcenter Downtime status
            // /** No Downtime */
            // const DOWNTIME_STATUS_NONE = 0;

            // /** Unplanned Downtime: Human */
            // const DOWNTIME_STATUS_UNPLAN_HUMAN = -1;
            // /** Unplanned Downtime: Machine */
            // const DOWNTIME_STATUS_UNPLAN_MACHINE = -2;
            // /** Unplanned Downtime: Die-Change */
            // const DOWNTIME_STATUS_UNPLAN_DIE_CHANGE = -3;

            // /** Planned Downtime: Die-Change */
            // const DOWNTIME_STATUS_PLAN_DIE_CHANGE = 3;
            // /** Planned Downtime: Break */
            // const DOWNTIME_STATUS_PLAN_BREAK = 4;
            // */

            $table->tinyInteger('event_type')->default(0)->index(); // 0: None, -1: Human, -2: machine, 3/-3: die change, 4 break
            $table->string('reason')->nullable();
            $table->string('user_input_reason')->nullable();
            $table->tinyInteger('broadcast_status')->default(0)->index(); // 0: Not broadcast, 1: start_time broadcasted, 2: end_time broadcasted

            //Timestamp
            $table->timestamp('start_time')->nullable()->index();
            $table->timestamp('end_time')->nullable()->index(); //null for ongoing
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
        Schema::dropIfExists('downtime_events');
    }
};
