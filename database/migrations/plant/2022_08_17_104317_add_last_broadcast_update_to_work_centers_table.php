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
        Schema::table('work_centers', function (Blueprint $table) {
            if (!Schema::hasColumn('work_centers', 'last_broadcast_update'))
                $table->integer('last_broadcast_update')->unsigned()->nullable()->after('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('work_centers', function (Blueprint $table) {
            if (Schema::hasColumn('work_centers', 'last_broadcast_update'))
                $table->dropColumn('last_broadcast_update');
        });
    }
};
