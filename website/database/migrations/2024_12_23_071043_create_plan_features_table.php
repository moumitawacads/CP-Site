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
        Schema::connection($this->connection)->create('plan_features', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('plan_id');
            $table->boolean('ability_to_create_an_account')->comment('ability_to_create_an_account');
            $table->boolean('ability_to_choose_a_payment_option')->comment('ability_to_choose_a_payment_option');
            $table->boolean('ability_to_share_sensitive_information')->comment('ability_to_share_sensitive_information');
            $table->boolean('ability_to_work_in_french_or_english')->comment('ability_to_work_in_french_or_english');
            $table->boolean('ability_to_turn_on_or_off_ai')->comment('ability_to_turn_on_or_off_ai');
            $table->boolean('ability_to_practice_sound_from_word_to_convert_in_english')->comment('ability_to_practice_any_sound_from_word_to_conversation_in_english');
            $table->boolean('ability_to_practice_sound_from_word_to_convert_in_french')->comment('ability_to_practice_any_sound_from_word_to_conversation_in_french');
            $table->boolean('ability_to_practice_any_blend_in_english')->comment('ability_to_practice_any_blend_in_english');
            $table->boolean('ability_to_practice_any_blend_in_french')->comment('ability_to_practice_any_blend_in_french');
            $table->boolean('ability_to_choose_any_number_of_syllables_in_english')->comment('ability_to_choose_any_number_of_syllables_in_english');
            $table->boolean('ability_to_choose_any_number_of_syllables_in_french')->comment('ability_to_choose_any_number_of_syllables_in_french');
            $table->boolean('ability_to_work_on_vowels_from_word_to_convert_in_english')->comment('ability_to_work_on_any_vowels_from_word_to_conversation_in_english');
            $table->boolean('ability_to_work_on_vowels_from_word_to_convert_in_french')->comment('ability_to_work_on_any_vowels_from_word_to_conversation_in_french');
            $table->boolean('ability_to_work_on_phonological_processes_to_convert_english')->comment('ability_to_work_on_phonological_processes_from_word_to_conversation_in_english');
            $table->boolean('ability_to_work_on_phonological_processes_to_convert_french')->comment('ability_to_work_on_phonological_processes_from_word_to_conversation_in_french');
            $table->boolean('ability_to_work_on_1_2_sounds_together')->comment('ability_to_work_on_1_2_sounds_together');
            $table->boolean('ability_to_work_on_multiple_sounds_together')->comment('ability_to_work_on_multiple_sounds_together');
            $table->boolean('ability_to_match_cards_or_items')->comment('ability_to_match_cards_or_items');
            $table->boolean('ability_to_customize_word_lists_based_on_chosen_sounds')->comment('ability_to_customize_word_lists_based_on_chosen_sounds');
            $table->boolean('ability_to_turn_on_prompts')->comment('ability_to_turn_on_prompts');
            $table->boolean('ability_to_have_multiple_types_of_cueing')->comment('ability_to_have_multiple_types_of_cueing');
            $table->boolean('access_to_limited_demonstration_videos')->comment('access_to_limited_demonstration_videos');
            $table->boolean('ability_to_view_activity_progress')->comment('ability_to_view_activity_progress');
            $table->boolean('ability_to_view_in_game_progress')->comment('ability_to_view_in_game_progress');
            $table->boolean('ability_to_level_up')->comment('ability_to_level_up');
            $table->boolean('ability_to_store_results_for_activities')->comment('ability_to_store_results_for_activities');
            $table->boolean('ability_for_the_phoneme_to_highlight_in_the_word')->comment('ability_for_the_phoneme_to_highlight_in_the_word');
            $table->boolean('ability_to_store_results_for_games_2_weeks')->comment('ability_to_store_results_for_games_2_weeks');
            $table->boolean('ability_to_store_results_for_games_for_2_months')->comment('ability_to_store_results_for_games_for_2_months');
            $table->boolean('ability_to_store_results_and_of_completion_for_a_world')->comment('ability_to_store_results_and_of_completion_for_a_world');
            $table->boolean('ability_to_complete_a_speech_screening')->comment('ability_to_complete_a_speech_screening');
            $table->boolean('ability_to_choose_any_world')->comment('ability_to_choose_any_world');
            $table->boolean('ability_to_work_in_groups_up_to_8_people')->comment('ability_to_work_in_groups_up_to_8_people');
            $table->boolean('ability_to_choose_any_game')->comment('ability_to_choose_any_game');
            $table->boolean('ability_to_choose_any_activity')->comment('ability_to_choose_any_activity');
            $table->boolean('ability_to_choose_a_character')->comment('ability_to_choose_a_character');
            $table->boolean('ability_to_earn_coins_or_figures_based_on_personal_perform')->comment('ability_to_earn_coins_or_figures_based_on_personal_performance');
            $table->boolean('ability_to_purchase_virtual_currency')->comment('ability_to_purchase_virtual_currency');
            $table->boolean('possibly_the_ability_to_buy_bonus_world_access')->comment('possibly_the_ability_to_buy_bonus_world_access');
            $table->boolean('ability_to_connect_to_clinician_or_learner')->comment('ability_to_connect_to_clinician_or_learner');
            $table->boolean('ability_to_share_results_with_clinician_or_guardian')->comment('ability_to_share_results_with_clinician_or_guardian');
            $table->boolean('ability_to_receive_clinician_feedback')->comment('ability_to_receive_clinician_feedback');
            $table->boolean('ability_to_customize_character')->comment('ability_to_customize_character');
            $table->boolean('ability_to_receive_monthly_reports')->comment('ability_to_receive_monthly_reports_of_users_sign_in_invite_accepted');
            $table->boolean('ability_to_find_a_friend_in_the_game')->comment('ability_to_find_a_friend_in_the_game');
            $table->boolean('ability_to_share_an_encrypted_invitation_to_a_clinician')->comment('ability_to_share_an_encrypted_invitation_to_a_clinician');
            $table->boolean('ability_to_share_an_encrypted_invitation')->comment('ability_to_share_an_encrypted_invitation_to_a_learner_or_parent');
            $table->boolean('ability_to_sign_up_for_an_account_with_a_clinicians_referral')->comment('ability_to_sign_up_for_an_account_with_a_clinicians_referral');
            $table->boolean('ability_to_share_the_app_with_family_members')->comment('ability_to_share_the_app_with_family_members');
            $table->boolean('ability_to_have_2_learner_profiles')->comment('ability_to_have_2_learner_profiles');
            $table->boolean('ability_to_interact_with_25_users_during_play')->comment('ability_to_interact_with_25_users_during_play');
            $table->boolean('ability_to_manage_profiles_through_the_app')->comment('ability_to_manage_profiles_through_the_app');
            $table->string('ability_to_manage_profiles_through_the_app_up_to')->comment('ability_to_manage_profiles_through_the_app_up_to');
            $table->boolean('ability_to_invite_user_to_homework_helper')->comment('ability_to_invite_user_to_homework_helper');
            $table->boolean('ability_to_assign_a_recommended_sound_as_homework')->comment('ability_to_assign_a_recommended_sound_as_homework');
            $table->boolean('ability_to_communicate_with_parents_about_results')->comment('ability_to_communicate_with_parents_about_results');
            $table->boolean('ability_to_assign_a_recommended_activity_or_game')->comment('ability_to_assign_a_recommended_activity_or_game');
            $table->boolean('ability_to_view_activity_progress_or_results_per_invite')->comment('ability_to_view_activity_progress_or_results_per_invite');
            $table->boolean('ability_to_view_avg_time_used_per_learner')->comment('ability_to_view_avg_time_used_per_learner');
            $table->boolean('ability_to_learners_at_each_level')->comment('ability_to_learners_at_each_level');
            $table->boolean('ability_to_see_the_number_of_learners_that_leveled_up')->comment('ability_to_see_the_number_of_learners_that_leveled_up');
            $table->boolean('ability_to_see_mastered_sounds')->comment('ability_to_see_mastered_sounds');
            $table->timestamps();

            $table->foreign('plan_id')->references('id')->on('plans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection($this->connection)->dropIfExists('plan_features');
    }
};
