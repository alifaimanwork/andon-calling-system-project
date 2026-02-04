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
        Schema::create('activity_logs', function (Blueprint $table) {
            //Primary key
            $table->id();

            //Foreign keys
            $table->unsignedInteger('user_id')->index();
            $table->unsignedInteger('plant_id')->nullable()->index();

            //Data
            $table->string('event_type');
            $table->string('event_title');
            $table->text('event_data');

            //Timestamp
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activity_logs');
    }
};
