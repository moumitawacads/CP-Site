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
        Schema::connection($this->connection)->create('learner_game_levels', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('learner_session_game_id');
            $table->string("audio_file_path")->nullable();
            $table->string("answer_type")->nullable();
            $table->timestamps();

            $table->foreign('learner_session_game_id')->references('id')->on('learner_session_games')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection($this->connection)->dropIfExists('learner_game_levels');
    }
};
