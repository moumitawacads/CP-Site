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
            $table->boolean("assessment_viewed")->default(0)->after("homework_helper_flag");
            $table->boolean("literacy_viewed")->default(0)->after("assessment_viewed");
            $table->boolean("homework_helper_viewed")->default(0)->after("literacy_viewed");
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
            $table->dropColumn("assessment_viewed");
            $table->dropColumn("literacy_viewed");
            $table->dropColumn("homework_helper_viewed");
        });
    }
};
