<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCallingsTable extends Migration
{
    public function up()
    {
        Schema::create('callings', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->boolean('state');
            $table->timestamp('start_time');
            $table->timestamp('end_time')->nullable();
            $table->unsignedInteger('work_center_id')->index();
            $table->string('work_center_uid')->index();
            $table->unsignedInteger('production_order_id')->nullable()->index();
            $table->string('production_order')->nullable()->index();
            $table->unsignedInteger('shift_type_id')->nullable()->index();
            $table->string('shift_name')->nullable()->index();
            $table->unsignedInteger('part_id')->nullable()->index();
            $table->string('part_number')->nullable()->index();
            $table->string('part_name')->nullable()->index();
            $table->integer('line_no')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('callings');
    }
}
