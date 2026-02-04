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
        Schema::create('monitor_clients', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('plant_id')->unsigned()->index();
            $table->bigInteger('target_id')->unsigned()->nullable()->index();
            $table->integer('client_type')->unsigned()->index();
            $table->string('name');
            $table->text('client_info')->nullable();
            $table->string('uid')->unique();
            $table->integer('state')->index();
            $table->tinyInteger('enabled')->default(1)->index();
            $table->timestamp('last_reported_at')->nullable()->index();
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
        Schema::dropIfExists('monitor_clients');
    }
};
