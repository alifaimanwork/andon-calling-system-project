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
        Schema::create('opc_active_tags', function (Blueprint $table) {
            //Primary key
            $table->increments('id');
            
            //Foreign keys
            $table->unsignedInteger('plant_id')->index()->nullable();
            $table->unsignedInteger('opc_server_id')->index();

            //Data
            $table->string('tag')->index();
            $table->string('data_type')->nullable();
            $table->string('value')->nullable();
            $table->string('prev_value')->nullable();
            $table->tinyInteger('state')->default(0);

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
        Schema::dropIfExists('opc_active_tags');
    }
};
