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
        Schema::create('gr_ng_logs', function (Blueprint $table) {
            //Primary key
            $table->id();

            //Foreign keys
            $table->unsignedInteger('production_line_id')->index();
            $table->unsignedInteger('user_id')->index();

            //Data
            $table->string('file_name');
            $table->string('export_path');
            $table->mediumText('data')->nullable();
            $table->tinyInteger('status')->default(0);

            //Timestamp
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gr_ng_logs');
    }
};
