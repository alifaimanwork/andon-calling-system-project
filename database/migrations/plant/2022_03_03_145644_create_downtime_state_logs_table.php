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
        Schema::create('downtime_state_logs', function (Blueprint $table) {
            //Primary key
            $table->id();
            
            //Foreign keys
            $table->unsignedInteger('work_center_id')->index();
            $table->unsignedInteger('downtime_id')->index();
            $table->unsignedInteger('opc_tag_id')->index()->nullable();

            //Data
            $table->tinyInteger('state');

            //Timestamp
            $table->timestamp('recorded_at')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('downtime_state_logs');
    }
};


