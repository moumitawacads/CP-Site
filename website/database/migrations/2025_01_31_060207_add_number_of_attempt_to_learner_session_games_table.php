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
        Schema::connection($this->connection)->table('learner_session_games', function (Blueprint $table) {
            $table->string('number_of_attempts')->nullable()->after('game_score');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection($this->connection)->table('learner_session_games', function (Blueprint $table) {
            $table->dropColumn('number_of_attempts');
        });
    }
};
