<template>
    <div v-if="learnerDetails && learnerDetails.learner_data.parent_data">
        <h5 class="modal-title mb-2">Virtual Language Instruction Readiness Form</h5>
        <table class="table table-bordered table-striped">
            <tbody>
                <tr>
                    <td>Does your child have access to a computer or tablet with a stable
                        internet connection?</td>
                    <td>{{
                        learnerDetails.learner_data.parent_data.user.virtual_language_instruction_agreed.access_to_a_computer_or_tablet
                            === 1 ? 'Yes' : 'No' }}</td>
                </tr>
                <tr>
                    <td>Does your child have a headset or microphone for clear audio
                        communication?</td>
                    <td>{{
                        learnerDetails.learner_data.parent_data.user.virtual_language_instruction_agreed.have_a_headset_or_microphone
                            === 1 ? 'Yes' : 'No' }}</td>
                </tr>
                <tr>
                    <td>Will an adult be available to assist your child during virtual
                        instruction sessions if needed?</td>
                    <td>{{
                        learnerDetails.learner_data.parent_data.user.virtual_language_instruction_agreed.adult_be_available_to_assist_your_child
                            === 1 ? 'Yes' : 'No' }}</td>
                </tr>
                <tr>
                    <td>Is there a quiet, distraction-free space for your child to
                        participate in virtual lessons?</td>
                    <td>{{
                        learnerDetails.learner_data.parent_data.user.virtual_language_instruction_agreed.distraction_free_space_for_your_child
                            === 1 ? 'Yes' : 'No' }}</td>
                </tr>
                <tr>
                    <td>Is your child comfortable participating in interactive activities
                        (e.g., answering questions, clicking on buttons) ?</td>
                    <td>{{
                        learnerDetails.learner_data.parent_data.user.virtual_language_instruction_agreed.comfortable_participating_in_interactive_activities
                            === 1 ? 'Yes' : 'No' }}</td>
                </tr>
            </tbody>
        </table>
        <h5 class="modal-title mt-4 mb-2">GDPR Permission</h5>
        <table class="table table-bordered table-striped">
            <tbody>
                <tr>
                    <td>Would you like to receive updates, promotions, and other marketing
                        communications from us via email and sms?</td>
                    <td>{{
                        learnerDetails.learner_data.parent_data.user.gdpr_agreed.receive_updates_promotions_and_other
                            === 1 ? 'Yes' : 'No' }}</td>
                </tr>
                <tr>
                    <td>Do you consent to the processing of your personal data for the
                        purposes outlined in our privacy policy?</td>
                    <td>{{
                        learnerDetails.learner_data.parent_data.user.gdpr_agreed.consent_to_the_processing_of_your_personal_data
                            === 1 ? 'Yes' : 'No' }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
export default {
    name: "DeclarationForms",
    props: ["learnerDetails"],
}
</script>

<style></style>