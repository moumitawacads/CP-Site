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
        Schema::connection($this->connection)->table('parents_latest_actions', function (Blueprint $table) {
            $table->string('dynamic_value')->nullable()->after('action_description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection($this->connection)->table('parents_latest_actions', function (Blueprint $table) {
            $table->dropColumn('dynamic_value');
        });
    }
};
