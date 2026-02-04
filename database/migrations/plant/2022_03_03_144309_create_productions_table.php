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
        Schema::create('productions', function (Blueprint $table) {
            //Primary key
            $table->increments('id');

            //Foreign keys
            $table->unsignedInteger('work_center_id')->index();
            $table->unsignedInteger('user_id')->index()->nullable();
            $table->unsignedInteger('shift_type_id')->index();

            //Data
            $table->integer('setup_time')->nullable();
            $table->mediumText('die_change_info')->nullable();
            $table->tinyInteger('status');

            
            $table->mediumText('hourly_summary')->nullable();

            //Summary Data
            $table->float('average_oee')->default(0);
            $table->float('average_availability')->default(0);
            $table->float('average_performance')->default(0);
            $table->float('average_quality')->default(0);

            $table->mediumText('runtime_summary_cache')->nullable();

            //-Snapshot
            $table->text('schedule_data');

            //Timestamp
            $table->date('shift_date')->index()->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('stopped_at')->nullable();
            $table->timestamp('die_change_end_at')->nullable();
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
        Schema::dropIfExists('productions');
    }
};
