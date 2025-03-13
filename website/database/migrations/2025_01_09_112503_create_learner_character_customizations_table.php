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
        Schema::connection($this->connection)->create('learner_character_customizations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('learner_id');
            $table->string('selected_bodycolor')->nullable();
            $table->string('selected_eyecolor')->nullable();
            $table->string('selected_eyebrow')->nullable();
            $table->string('selected_hair')->nullable();
            $table->string('selected_upperwear')->nullable();
            $table->string('selected_pant')->nullable();
            $table->string('selected_backpack')->nullable();
            $table->string('selected_shoe')->nullable();
            $table->string('selected_hat')->nullable();
            $table->string('selected_glove')->nullable();
            $table->string('selected_glasses')->nullable();
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
        Schema::connection($this->connection)->dropIfExists('learner_character_customizations');
    }
};
