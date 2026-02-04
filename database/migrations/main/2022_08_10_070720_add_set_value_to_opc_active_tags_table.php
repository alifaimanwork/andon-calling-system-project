<?php

use App\Models\Plant;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;
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
        Schema::table('opc_active_tags', function (Blueprint $table) {
            if (!Schema::hasColumn('opc_active_tags', 'set_value'))
                $table->string('set_value')->nullable()->after('prev_value');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('opc_active_tags', function (Blueprint $table) {
            if (Schema::hasColumn('opc_active_tags', 'set_value'))
                $table->dropColumn('set_value');
        });
    }
};
