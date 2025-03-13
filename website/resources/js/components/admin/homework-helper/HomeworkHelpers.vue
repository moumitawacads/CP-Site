<template>
    <div>
        <h2 class="mb-4">Homework Helpers</h2>
        <div class="row">
            <div class="col-md-3">
                <input type="text" class="form-control mb-3" placeholder="Search..." v-model="searchQuery" />
            </div>
            <div class="col-md-4"></div>
            <div class="col-md-2"></div>
            <div class="col-md-2 d-flex justify-content-end">

            </div>
        </div>

        <table class="table table-bordered" v-if="homeworkHelpersList">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Learner Name</th>
                    <th>Parents Name</th>
                    <th>Clinician Name</th>
                    <th style="width: 130px">Actions</th>
                </tr>
            </thead>
            <tbody>
                <template v-if="filteredHomeworkHelpersList.length === 0">
                    <tr>
                        <td colspan="7" class="text-center">No data found</td>
                    </tr>
                </template>
                <template v-else>
                    <tr v-for="(homeworkHelper, index) in filteredHomeworkHelpersList" :key="homeworkHelper.id">
                        <td>{{ index + 1 }}</td>
                        <td>{{ homeworkHelper.learner_data.user.fullname }}</td>
                        <td>{{ homeworkHelper.learner_data.parent_data ?
                            homeworkHelper.learner_data.parent_data.user.fullname : "" }}</td>
                        <td>{{ homeworkHelper.clinician_data ? homeworkHelper.clinician_data.user.fullname : '' }}</td>
                        <td>
                            <button class="btn btn-primary btn-sm" v-if="!homeworkHelper.clinician_data"
                                @click="assignHomeworkHelper(homeworkHelper.homework_helper_data.id)">
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
    name: "HomeworkHelpers",
    data() {
        return {
            homeworkHelpersList: null,
            searchQuery: "",
        };
    },
    computed: {
        filteredHomeworkHelpersList() {
            return this.homeworkHelpersList
                ? this.homeworkHelpersList.filter((item) => {
                    const matchesLearnerSearchQuery = item.learner_data.user.fullname
                        .toLowerCase()
                        .includes(this.searchQuery.toLowerCase());
                    const matchesParentsSearchQuery = item.learner_data.parent_data ? item.learner_data.parent_data.user.fullname
                        .toLowerCase()
                        .includes(this.searchQuery.toLowerCase()) : "";
                    const matchesClinicianSearchQuery = item.clinician_data ? item.clinician_data.user.fullname
                        .toLowerCase()
                        .includes(this.searchQuery.toLowerCase()) : "";
                    return matchesLearnerSearchQuery || matchesParentsSearchQuery || matchesClinicianSearchQuery;
                })
                : [];
        },
    },
    mounted() {
        this.getAllHomeworkHelpersList();
    },
    methods: {
        async getAllHomeworkHelpersList() {
            try {
                const response = await axios.get("/api/admin/homework-helpers", {
                    headers: {
                        Authorization: `Bearer ${this.$store.state.token}`,
                    },
                });
                this.homeworkHelpersList = response.data.data;
            } catch (error) {
                console.error(error);
            }
        },
        assignHomeworkHelper(homeworkHelperId) {
            this.$router.push({ name: "assign-homework-helper", params: { id: homeworkHelperId } });
        }
    },
}
</script>

<style scoped></style>