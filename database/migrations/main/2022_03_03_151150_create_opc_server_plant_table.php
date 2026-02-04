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
        Schema::create('opc_server_plant', function (Blueprint $table) {
            //Primary key
            $table->id();
            
            //Foreign keys
            $table->unsignedInteger('opc_server_id')->index();
            $table->unsignedInteger('plant_id')->index();

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
        Schema::dropIfExists('opc_server_plant');
    }
};


