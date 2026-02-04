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
        Schema::create('rejects', function (Blueprint $table) {
            //Primary key
            $table->id();

            //Foreign keys
            $table->unsignedInteger('production_line_id')->index();
            $table->unsignedInteger('reject_type_id')->index();
            $table->unsignedInteger('user_id')->index()->nullable();

            //Data
            $table->integer('count');

            //Timestamp
            $table->timestamp('recorded_at')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rejects');
    }
};
