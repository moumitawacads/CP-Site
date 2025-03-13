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
        Schema::connection($this->connection)->create('learner_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('learner_id');
            $table->string("session_start_time")->nullable();
            $table->string("atrium")->nullable();
            $table->string("world")->nullable();
            $table->string("target_sound")->nullable();
            $table->string("pick_possition")->nullable();
            $table->string("pick_syllabus")->nullable();
            $table->string("spell_word")->nullable();
            $table->string("session_end_time")->nullable();
            $table->timestamps();

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
        Schema::connection($this->connection)->dropIfExists('learner_sessions');
    }
};
