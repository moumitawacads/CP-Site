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
            $table->unsignedBigInteger('assign_by')->nullable()->after('scheduled_meeting_timezone');

            $table->foreign('assign_by')->references('id')->on('users')->onDelete('cascade');
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
            $table->dropColumn("assign_by");
        });
    }
};
