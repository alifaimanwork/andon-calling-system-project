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
        Schema::create('plants', function (Blueprint $table) {
            //Primary key
            $table->increments('id');
            
            //Foreign keys
            $table->unsignedInteger('region_id')->index()->nullable();
            $table->unsignedInteger('company_id')->index()->nullable();

            //Data
            $table->string('uid')->unique();
            $table->string('sap_id');
            $table->string('name');
            $table->string('time_zone');
            $table->string('total_employee')->nullable();
            $table->string('total_production_line')->nullable();
            $table->mediumText('overview_layout_data')->nullable();
            $table->text('database_configurations');

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
        Schema::dropIfExists('plants');
    }
};
