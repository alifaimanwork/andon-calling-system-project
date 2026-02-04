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
        Schema::create('monitor_client_logs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('monitor_client_id')->unsigned()->index();
            $table->integer('state');
            $table->text('client_info')->nullable();
            $table->timestamp('recorded_at')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('monitor_client_logs');
    }
};
