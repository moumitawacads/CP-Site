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
        Schema::connection($this->connection)->create('learner_session_games', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('learner_session_id');
            $table->string("game_name")->nullable();
            $table->string("game_score")->nullable();
            $table->timestamps();

            $table->foreign('learner_session_id')->references('id')->on('learner_sessions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection($this->connection)->ropIfExists('learner_session_games');
    }
};
