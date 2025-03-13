<template>
    <div>
        <h2 class="mb-4">Literacy Diagnostics</h2>
        <div class="row">
            <div class="col-md-3">
                <input type="text" class="form-control mb-3" placeholder="Search..." v-model="searchQuery" />
            </div>
            <div class="col-md-4"></div>
            <div class="col-md-2"></div>
            <div class="col-md-2 d-flex justify-content-end">

            </div>
        </div>

        <table class="table table-bordered" v-if="literacyDiagnosticsList">
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
                <template v-if="filteredLiteracyDiagnosticsList.length === 0">
                    <tr>
                        <td colspan="7" class="text-center">No data found</td>
                    </tr>
                </template>
                <template v-else>
                    <tr v-for="(literacy, index) in filteredLiteracyDiagnosticsList" :key="literacy.id">
                        <td>{{ index + 1 }}</td>
                        <td>{{ literacy.learner_data.user.fullname }}</td>
                        <td>{{ literacy.user_data.fullname }}</td>
                        <td>{{ literacy.clinician_data ? literacy.clinician_data.user.fullname : '' }}</td>
                        <td>{{ literacy.literacy_data.scheduled_meeting_start_datetime }}</td>
                        <td>{{ literacy.literacy_data.scheduled_meeting_end_datetime }}</td>
                        <td>
                            <button class="btn btn-primary btn-sm" v-if="!literacy.clinician_data"
                                @click="assignLiteracy(literacy.literacy_data.id)">
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
    name: "LiteracyDiagnostics",
    data() {
        return {
            literacyDiagnosticsList: null,
            searchQuery: "",
        };
    },
    computed: {
        filteredLiteracyDiagnosticsList() {
            return this.literacyDiagnosticsList
                ? this.literacyDiagnosticsList.filter((item) => {
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
        this.getAllLiteracyDiagnosticList();
    },
    methods: {
        async getAllLiteracyDiagnosticList() {
            try {
                const response = await axios.get("/api/admin/literacy-diagnostic", {
                    headers: {
                        Authorization: `Bearer ${this.$store.state.token}`,
                    },
                });
                this.literacyDiagnosticsList = response.data.data;
            } catch (error) {
                console.error(error);
            }
        },
        assignLiteracy(literacyId) {
            this.$router.push({ name: "assign-literacy-diagnostic", params: { id: literacyId } });
        }
    },
}
</script>

<style scoped></style>