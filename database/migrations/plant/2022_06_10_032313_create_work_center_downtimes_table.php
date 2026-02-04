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
        Schema::create('work_center_downtimes', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('downtime_id')->index();
            $table->unsignedInteger('work_center_id')->index();
            $table->unsignedInteger('opc_tag_id')->nullable()->index();
            $table->tinyInteger('state')->index()->default(0);
            $table->timestamp('value_updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('work_center_downtimes');
    }
};
