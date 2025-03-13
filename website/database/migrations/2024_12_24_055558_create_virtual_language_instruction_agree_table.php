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
        Schema::connection($this->connection)->create('virtual_language_instruction_agree', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->boolean('access_to_a_computer_or_tablet')->nullable();
            $table->boolean('have_a_headset_or_microphone')->nullable();
            $table->boolean('adult_be_available_to_assist_your_child')->nullable();
            $table->boolean('distraction_free_space_for_your_child')->nullable();
            $table->boolean('comfortable_participating_in_interactive_activities')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection($this->connection)->dropIfExists('virtual_language_instruction_agree');
    }
};
