<template>
    <div>
        <div class="accordion p-12" id="accordionServices">
            <div class="accordion-item"
                v-if="hasAssessments && learnerDetails && learnerDetails.learner_data.assessments.length > 0">
                <div v-for="assessment in learnerDetails.learner_data.assessments" :key="assessment.id">
                    <h2 class="accordion-header" id="assessment">
                        <button class="accordion-button collapsed btn-focused" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                            <b>Assessments</b>
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="assessment"
                        data-bs-parent="#accordionServices">
                        <div class="accordion-body">
                            <p><b>Scheduled Meeting Start Time:</b> {{ assessment.scheduled_meeting_start_datetime }}
                            </p>
                            <p><b>Scheduled Meeting End Time:</b> {{ assessment.scheduled_meeting_start_datetime }}</p>
                            <p><b>Clinician Name:</b> {{ assessment.clinician ? assessment.clinician.user.fullname : ""
                                }}</p>
                            <p v-if="$store.state.user.role_slug === 'clinician'"><b>Assigned By:</b> {{
                                assessment.assign_by ? assessment.assign_by.fullname : "" }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-item"
                v-if="hasLiteracyDiagnostics && learnerDetails && learnerDetails.learner_data.literacy_diagnostics.length > 0">
                <div v-for="literacy_diagnostic in learnerDetails.learner_data.literacy_diagnostics"
                    :key="literacy_diagnostic.id">
                    <h2 class="accordion-header" id="literacy-diagnostic">
                        <button class="accordion-button collapsed btn-focused" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            <b>Literacy Diagnostics</b>
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="literacy-diagnostic"
                        data-bs-parent="#accordionServices">
                        <div class="accordion-body">
                            <p><b>Scheduled Meeting Start Time:</b> {{
                                literacy_diagnostic.scheduled_meeting_start_datetime }}</p>
                            <p><b>Scheduled Meeting End Time:</b> {{
                                literacy_diagnostic.scheduled_meeting_start_datetime }}</p>
                            <p><b>Clinician Name:</b> {{ literacy_diagnostic.clinician ?
                                literacy_diagnostic.clinician.user.fullname : "" }}</p>
                            <p v-if="$store.state.user.role_slug === 'clinician'"><b>Assigned By:</b> {{
                                literacy_diagnostic.assign_by ?
                                    literacy_diagnostic.assign_by.fullname : "" }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-item"
                v-if="hasHomeworkHelpers && learnerDetails && learnerDetails.learner_data.homework_helpers.length > 0">
                <div v-for="homework_helper in learnerDetails.learner_data.homework_helpers" :key="homework_helper.id">
                    <h2 class="accordion-header" id="homework-helpers">
                        <button class="accordion-button collapsed btn-focused" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            <b>Homework Helpers</b>
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="homework-helpers"
                        data-bs-parent="#accordionServices">
                        <div class="accordion-body">
                            <p><b>Clinician Name:</b> {{ homework_helper.clinician ?
                                homework_helper.clinician.user.fullname : "" }}</p>
                            <p v-if="$store.state.user.role_slug === 'clinician' && homework_helper.assign_by">
                                <b>Assigned By:</b> {{
                                    homework_helper.assign_by ?
                                        homework_helper.assign_by.fullname : "" }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

export default {
    name: "Services",
    props: ["learnerDetails"],
    computed: {
        hasAssessments() {
            let show = this.learnerDetails.learner_data.assessments.filter(assessment => {
                return assessment.clinician_id === this.$store.state.providerData.id;
            }).length > 0;
            return this.$store.state.user ? (this.$store.state.user.role_slug === "clinician" ? show : true) : false
        },
        hasLiteracyDiagnostics() {
            let show = this.learnerDetails.learner_data.literacy_diagnostics.filter(literacyDiagnostic => {
                return literacyDiagnostic.clinician_id === this.$store.state.providerData.id;
            }).length > 0;
            return this.$store.state.user ? (this.$store.state.user.role_slug === "clinician" ? show : true) : false
        },
        hasHomeworkHelpers() {
            let show = this.learnerDetails.learner_data.homework_helpers.filter(homeworkHelper => {
                return homeworkHelper.clinician_id === this.$store.state.providerData.id;
            }).length > 0;
            return this.$store.state.user ? (this.$store.state.user.role_slug === "clinician" ? show : true) : false
        }
    }
}
</script>

<style scoped>
.btn-focused {
    background-color: #cfe2ff;
    color: #052c65;
}
</style>