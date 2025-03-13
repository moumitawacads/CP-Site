<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'encrypted_mysql';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection($this->connection)->table('learners_encrypt_data', function (Blueprint $table) {
            $table->string('user_id')->nullable()->after('clinician_id');
            $table->string('parent_id')->nullable()->after('user_id');
            $table->string('learner_phone_number')->nullable()->after('learner_email');
            $table->string('flag')->nullable()->default(1)->after('learner_phone_number')->comment("1 means active and 0 means inactive");
            $table->string('learner_code')->nullable()->after('flag');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection($this->connection)->table('learners_encrypt_data', function (Blueprint $table) {
            $table->dropColumn('user_id');
            $table->dropColumn('parent_id');
            $table->dropColumn('learner_phone_number');
            $table->dropColumn('flag');
            $table->dropColumn('learner_code');
        });
    }
};
