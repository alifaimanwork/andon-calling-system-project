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
        Schema::create('production_lines', function (Blueprint $table) {
            // Primary key
            $table->increments('id');

            // Foreign keys
            $table->unsignedInteger('production_order_id')->index();
            $table->unsignedInteger('production_id')->index();

            // Data
            $table->unsignedInteger('line_no')->index();

            // Snapshot
            $table->text('part_data');

            // Production Data
            $table->integer('plan_quantity')->default(0);
            $table->integer('actual_output')->default(0);
            $table->integer('reject_count')->default(0);
            $table->integer('ok_count')->default(0);

            $table->integer('pending_count')->default(0);
            $table->integer('pending_ok')->default(0);
            $table->integer('pending_ng')->default(0);
            $table->tinyInteger('rework_status')->default(0); // 0: open, 1: completed

            $table->mediumText('reject_summary')->nullable();
            $table->mediumText('hourly_summary')->nullable();
            $table->mediumText('overall_summary')->nullable();

            // Summary (on stopped)
            $table->float('oee')->default(0);
            $table->float('availability')->default(0);
            $table->float('performance')->default(0);
            $table->float('quality')->default(0);
            $table->float('standard_output')->default(0);

            // Timestamp
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
        Schema::dropIfExists('production_lines');
    }
};
