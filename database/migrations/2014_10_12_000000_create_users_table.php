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
        Schema::create('users', function (Blueprint $table) {
            //Primary key
            $table->increments('id');

            //Foreign keys
            $table->unsignedInteger('company_id')->index()->nullable();
            $table->unsignedInteger('plant_id')->index()->nullable();

            //Data
            $table->string('staff_no')->unique();
            $table->string('sap_id')->nullable();
            $table->tinyInteger('role')->index()->default(2); //0: super admin, 1: plant admin, 2: user
            $table->string('full_name')->nullable();
            $table->string('email')->index();
            $table->tinyInteger('enabled')->index()->default(1);
            $table->string('designation')->nullable();
            $table->string('profile_picture')->nullable();
            $table->string('password');
            $table->rememberToken();

            //Timestamp
            $table->timestamp('email_verified_at')->nullable();
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
        Schema::dropIfExists('users');
    }
};

