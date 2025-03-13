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
        Schema::connection($this->connection)->table('user_literacy_diagnostics', function (Blueprint $table) {
            $table->string("opportunity_id")->nullable()->after("calendar_webhook_response");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection($this->connection)->table('user_literacy_diagnostics', function (Blueprint $table) {
            $table->dropColumn("opportunity_id");
        });
    }
};
