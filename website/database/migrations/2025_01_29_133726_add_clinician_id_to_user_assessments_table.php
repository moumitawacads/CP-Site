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
        Schema::connection($this->connection)->table('user_assessments', function (Blueprint $table) {
            $table->unsignedBigInteger('clinician_id')->nullable()->after('learner_id');

            $table->foreign('clinician_id')->references('id')->on('clinicians')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection($this->connection)->table('user_assessments', function (Blueprint $table) {
            $table->dropColumn('clinician_id');
        });
    }
};
