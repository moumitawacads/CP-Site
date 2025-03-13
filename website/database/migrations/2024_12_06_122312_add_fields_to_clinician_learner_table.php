<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mysql';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection($this->connection)->table('clinician_learner', function (Blueprint $table) {
            $table->string('homework_helper_flag')->nullable()->default(0)->comment("1 means On and 0 means Off")->after('learner_encrypt_data_id');
            $table->string('parent_approval_received')->nullable()->after('homework_helper_flag');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection($this->connection)->table('clinician_learner', function (Blueprint $table) {
            $table->dropColumn('homework_helper_flag');
            $table->dropColumn('parent_approval_received');
        });
    }
};
