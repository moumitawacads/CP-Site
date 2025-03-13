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
        Schema::connection($this->connection)->create('parents_latest_actions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parents_id');
            $table->string('tab_id')->nullable();
            $table->text('action_description')->nullable();
            $table->timestamps();

            $table->foreign('parents_id')->references('id')->on('parents')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection($this->connection)->dropIfExists('parents_latest_actions');
    }
};
