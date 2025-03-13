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
        Schema::connection($this->connection)->table('learner_game_levels', function (Blueprint $table) {
            $table->string('words')->nullable()->after('audio_file_path');
            $table->string('two_words_phrase')->nullable()->after('words');
            $table->string('silly_sentence')->nullable()->after('two_words_phrase');
            $table->string('story')->nullable()->after('silly_sentence');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection($this->connection)->table('learner_game_levels', function (Blueprint $table) {
            $table->dropColumn(['words', 'two_words_phrase', 'silly_sentence', 'story']);
        });
    }
};
