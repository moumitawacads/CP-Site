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
        Schema::connection($this->connection)->table('users', function (Blueprint $table) {
            $table->string('last_login_datetime')->nullable()->after('email_verified_at');
            $table->string('last_logout_datetime')->nullable()->after('last_login_datetime');
            $table->string('session_datetime')->nullable()->after('last_logout_datetime');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection($this->connection)->table('users', function (Blueprint $table) {
            $table->dropColumn('last_login_datetime');
            $table->dropColumn('last_logout_datetime');
            $table->dropColumn('session_datetime');
        });
    }
};
