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
        Schema::connection($this->connection)->create('clinician_system_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('clinician_id');
            $table->boolean('ai_control_flag')->nullable()->default(0)->comment("O means Off and 1 means On");
            $table->boolean('homework_helper_flag')->nullable()->default(0)->comment("O means Off and 1 means On");
            $table->boolean('pre_set_videos_flag')->nullable()->default(0)->comment("O means Off and 1 means On");
            $table->boolean('cues_flag')->nullable()->default(0)->comment("O means Off and 1 means On");
            $table->boolean('prompts_flag')->nullable()->default(0)->comment("O means Off and 1 means On");
            $table->boolean('zoom_mode_flag')->nullable()->default(0)->comment("O means Off and 1 means On");
            $table->timestamps();

            $table->foreign('clinician_id')->references('id')->on('clinicians')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection($this->connection)->dropIfExists('clinician_system_settings');
    }
};
