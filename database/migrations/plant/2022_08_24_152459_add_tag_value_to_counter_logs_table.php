<?php

use App\Models\WorkCenter;
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
        Schema::table('counter_logs', function (Blueprint $table) {
            $table->integer('count')->default(0)->change();
            if (!Schema::hasColumn('counter_logs', 'tag_value'))
                $table->integer('tag_value')->nullable()->after('count');

            if (!Schema::hasColumn('counter_logs', 'work_center_status'))
                $table->tinyInteger('work_center_status')->unsigned()->default(WorkCenter::STATUS_RUNNING)->index()->after('tag_value');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('counter_logs', function (Blueprint $table) {
            if (Schema::hasColumn('counter_logs', 'tag_value'))
                $table->dropColumn('tag_value');

            if (Schema::hasColumn('counter_logs', 'work_center_status'))
                $table->dropColumn('work_center_status');
        });
    }
};
