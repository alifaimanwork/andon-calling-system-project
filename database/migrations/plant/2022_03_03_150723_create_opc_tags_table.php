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
        Schema::create('opc_tags', function (Blueprint $table) {
            //Primary key
            $table->increments('id');
            
            //Foreign keys
            $table->unsignedInteger('plant_id')->index();
            $table->unsignedInteger('work_center_id')->index();
            $table->unsignedInteger('opc_tag_type_id')->index();
            $table->unsignedInteger('opc_server_id')->index()->nullable();

            //Data
            $table->string('info')->nullable();
            $table->string('tag')->index();
            $table->string('value')->nullable();
            $table->string('prev_value')->nullable();

            //Timestamp
            $table->timestamp('value_updated_at')->nullable();
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
        Schema::dropIfExists('opc_tags');
    }
};


