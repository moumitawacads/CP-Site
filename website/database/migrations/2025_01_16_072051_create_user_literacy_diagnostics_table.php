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
        Schema::connection($this->connection)->create('user_literacy_diagnostics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('learner_id')->nullable();
            $table->string('learner_unique_code')->nullable();
            $table->string('scheduled_meeting_start_datetime')->nullable();
            $table->string('scheduled_meeting_end_datetime')->nullable();
            $table->string('scheduled_meeting_timezone')->nullable();
            $table->boolean('status')->default(0)->comment('0 means not completed 1 means completed');
            $table->json('calendar_webhook_response')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('learner_id')->references('id')->on('learners')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection($this->connection)->dropIfExists('user_literacy_diagnostics');
    }
};
