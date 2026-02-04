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
        Schema::create('counter_logs', function (Blueprint $table) {
            //Primary key
            $table->id();
            
            //Foreign keys
            $table->unsignedInteger('work_center_id')->index();
            $table->unsignedInteger('opc_tag_id')->index()->nullable();
            $table->unsignedInteger('production_line_id')->index()->nullable();

            //Data
            $table->unsignedInteger('line_no')->index()->nullable();
            $table->integer('count');

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
        Schema::dropIfExists('counter_logs');
    }
};


