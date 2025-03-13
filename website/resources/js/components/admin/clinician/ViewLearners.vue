<template>
    <div class="">
        <h2 class="mb-4">Learners List</h2>
        <h6 class="mb-4">Clinician Name: {{ getClinicianName() }}</h6>
        <div class="row">
            <!-- <div class="col-md-3">
                <input type="text" class="form-control mb-3" placeholder="Search..." v-model="searchQuery" />
            </div> -->
            <div class="col-md-4"></div>
            <div class="col-md-2"></div>
            <div class="col-md-2 d-flex justify-content-end">

            </div>
        </div>

        <table class="table table-bordered" v-if="learnersList">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fullname</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Parents Name</th>
                </tr>
            </thead>
            <tbody>
                <template v-if="filteredLearnersList.length === 0">
                    <tr>
                        <td colspan="7" class="text-center">No data found</td>
                    </tr>
                </template>
                <template v-else>
                    <tr v-for="(learner, index) in filteredLearnersList" :key="learner.id">
                        <td>{{ index + 1 }}</td>
                        <td>{{ learner.learner_data.user.fullname }}</td>
                        <td>{{ learner.learner_data.user.email }}</td>
                        <td>{{ learner.learner_data.user.phone_number }}</td>
                        <td>{{ learner.learner_data.parent_data ? learner.learner_data.parent_data.user.fullname : ''
                            }}</td>
                    </tr>
                </template>
            </tbody>
        </table>
        <div class="mt-3">
            <router-link to="/admin/clinicians" class="btn btn-secondary" style="margin-left: 8px">Back</router-link>
        </div>
    </div>
</template>

<script>
export default {
    name: "ViewLearners",
    data() {
        return {
            learnersList: null,
            searchQuery: "",
        };
    },
    computed: {
        filteredLearnersList() {
            return this.learnersList
                ? this.learnersList.filter((item) => {
                    const matchesSearchQuery = item.learner_data.user.fullname
                        .toLowerCase()
                        .includes(this.searchQuery.toLowerCase());
                    return matchesSearchQuery;
                })
                : [];
        },
    },
    mounted() {
        this.getAllLearnersByClinicianId();
    },
    methods: {
        async getAllLearnersByClinicianId() {
            try {
                const response = await axios.get("/api/admin/learner/?clinician_id=" + this.$route.params.id, {
                    headers: {
                        Authorization: `Bearer ${this.$store.state.token}`,
                    },
                });
                this.learnersList = response.data.data;
            } catch (error) {
                console.error(error);
            }
        },
        getClinicianName() {
            let clinician = this.learnersList ? this.learnersList[0].learner_data.clinicians_data.filter((clinician) => {
                return clinician.clinician_data.id == this.$route.params.id;
            }) : "";

            return clinician.length > 0 ? clinician[0].clinician_data.user.fullname : "";
        }
    },
}
</script>

<style></style>