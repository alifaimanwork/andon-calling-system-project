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
        Schema::create('opc_servers', function (Blueprint $table) {
            //Primary key
            $table->increments('id');
            
            //Fields
            $table->string('name');
            $table->string('hostname');
            $table->integer('port');
            $table->string('adapter_hostname')->default('127.0.0.1');
            $table->integer('adapter_port')->default(8000);
            $table->text('configuration_data')->nullable();
            $table->mediumText('tags')->nullable();

            //Timestamp
            $table->timestamp('tags_updated_at')->nullable();
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
        Schema::dropIfExists('opc_servers');
    }
};


