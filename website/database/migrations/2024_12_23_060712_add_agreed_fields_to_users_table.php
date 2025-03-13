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
        Schema::connection($this->connection)->table('users', function (Blueprint $table) {
            $table->boolean('consent_of_speech_language_flag')->nullable();
            $table->string('consent_of_speech_language_datetime')->nullable();
            $table->boolean('virtual_readiness_assessment_flag')->nullable();
            $table->string('virtual_readiness_assessment_datetime')->nullable();
            $table->boolean('gdpr_flag')->nullable();
            $table->string('gdpr_datetime')->nullable();
            $table->longText('additional_comments')->nullable();
            $table->string('scheduled_meeting_start_datetime')->nullable();
            $table->string('scheduled_meeting_end_datetime')->nullable();
            $table->string('scheduled_meeting_timezone')->nullable();
            $table->json('calendar_webhook_response')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection($this->connection)->table('users', function (Blueprint $table) {
            $table->dropColumn('consent_of_speech_language_flag');
            $table->dropColumn('consent_of_speech_language_datetime');
            $table->dropColumn('virtual_readiness_assessment_flag');
            $table->dropColumn('virtual_readiness_assessment_datetime');
            $table->dropColumn('gdpr_flag');
            $table->dropColumn('gdpr_datetime');
        });
    }
};
