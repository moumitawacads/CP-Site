<template>
    <div class="">
        <h2 class="mb-4">Assessments</h2>
        <div class="row">
            <div class="col-md-3">
                <input type="text" class="form-control mb-3" placeholder="Search..." v-model="searchQuery" />
            </div>
            <div class="col-md-4"></div>
            <div class="col-md-2"></div>
            <div class="col-md-2 d-flex justify-content-end">

            </div>
        </div>

        <table class="table table-bordered" v-if="assessmentsList">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Learner Name</th>
                    <th>Parents Name</th>
                    <th>Clinician Name</th>
                    <th>Schedule Meeting Start Time</th>
                    <th>Schedule Meeting End Time</th>
                    <th style="width: 130px">Actions</th>
                </tr>
            </thead>
            <tbody>
                <template v-if="filteredAssessmentList.length === 0">
                    <tr>
                        <td colspan="7" class="text-center">No data found</td>
                    </tr>
                </template>
                <template v-else>
                    <tr v-for="(assessment, index) in filteredAssessmentList" :key="assessment.id">
                        <td>{{ index + 1 }}</td>
                        <td>{{ assessment.learner_data.user.fullname }}</td>
                        <td>{{ assessment.user_data.fullname }}</td>
                        <td>{{ assessment.clinician_data ? assessment.clinician_data.user.fullname : '' }}</td>
                        <td>{{ assessment.assessment_data.scheduled_meeting_start_datetime }}</td>
                        <td>{{ assessment.assessment_data.scheduled_meeting_end_datetime }}</td>
                        <td>
                            <button class="btn btn-primary btn-sm" v-if="!assessment.clinician_data"
                                @click="assignAssessment(assessment.assessment_data.id)">
                                Assign
                            </button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
</template>

<script>
export default {
    name: "Assessments",
    data() {
        return {
            assessmentsList: null,
            searchQuery: "",
        };
    },
    computed: {
        filteredAssessmentList() {
            return this.assessmentsList
                ? this.assessmentsList.filter((item) => {
                    const matchesLearnerSearchQuery = item.learner_data.user.fullname
                        .toLowerCase()
                        .includes(this.searchQuery.toLowerCase());
                    const matchesParentsSearchQuery = item.user_data.fullname
                        .toLowerCase()
                        .includes(this.searchQuery.toLowerCase());
                    const matchesClinicianSearchQuery = item.clinician_data ? item.clinician_data.user.fullname
                        .toLowerCase()
                        .includes(this.searchQuery.toLowerCase()) : '';
                    return matchesLearnerSearchQuery || matchesParentsSearchQuery || matchesClinicianSearchQuery;
                })
                : [];
        },
    },
    mounted() {
        this.getAllAssessmentList();
    },
    methods: {
        async getAllAssessmentList() {
            try {
                const response = await axios.get("/api/admin/assessment", {
                    headers: {
                        Authorization: `Bearer ${this.$store.state.token}`,
                    },
                });
                this.assessmentsList = response.data.data;
            } catch (error) {
                console.error(error);
            }
        },
        assignAssessment(assessmentId) {
            this.$router.push({ name: "assign-assessment", params: { id: assessmentId } });
        }
    },
}
</script>

<style></style>