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
        Schema::connection($this->connection)->create('learners_encrypt_data', function (Blueprint $table) {
            $table->id();
            $table->string('clinician_id')->nullable();
            $table->string('speech_language_diagnosis')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('clinician_name')->nullable();
            $table->string('gender')->nullable();
            $table->string('name_of_school')->nullable();
            $table->string('grade')->nullable();
            $table->string('culture')->nullable();
            $table->string('family_diagnosis')->nullable();
            $table->string('history')->nullable();
            $table->string('learner_type')->nullable();
            $table->string('learner_email')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection($this->connection)->dropIfExists('learners_encrypt_data');
    }
};
