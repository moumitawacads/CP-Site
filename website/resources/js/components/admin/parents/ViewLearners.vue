<template>
    <div class="">
        <h2 class="mb-4">Learners List</h2>
        <h6 class="mb-4">Parent Name: {{ getParentName() }}</h6>
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
                    <th>Clinicians Name</th>
                    <th>Sevices</th>
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
                        <td class="p-0">
                            <table class="table m-0">
                                <tbody>
                                    <tr v-for="(clinician, indexC) in learner.learner_data.clinicians_data"
                                        :key="clinician.clinician_data.id">
                                        <td
                                            :class="learner.learner_data.clinicians_data.length === (indexC + 1) ? 'bb-0' : ''">
                                            {{ clinician.clinician_data.user.fullname }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                        <td class="p-0">
                            <table class="table m-0">
                                <tbody>
                                    <tr v-for="(clinician, indexS) in learner.learner_data.clinicians_data"
                                        :key="clinician.clinician_data.id">
                                        <td
                                            :class="learner.learner_data.clinicians_data.length === (indexS + 1) ? 'bb-0' : ''">
                                            <span class="badge"
                                                :class="getClassName(clinician.clinician_learner_link_data.service_type)">{{
                                                    clinician.clinician_learner_link_data.service_type }}</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
        <div class="mt-3">
            <router-link to="/admin/parents" class="btn btn-secondary" style="margin-left: 8px">Back</router-link>
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
        this.getAllLearnersByParentId();
    },
    methods: {
        async getAllLearnersByParentId() {
            try {
                const response = await axios.get("/api/admin/learner/?parent_id=" + this.$route.params.id, {
                    headers: {
                        Authorization: `Bearer ${this.$store.state.token}`,
                    },
                });
                this.learnersList = response.data.data;
            } catch (error) {
                console.error(error);
            }
        },
        getClinicianNames(clinicians_data) {
            let clinicianNames = "";
            clinicians_data.forEach((clinician, index) => {
                clinicianNames += clinician.clinician_data.user.fullname + ((index + 1) < clinicians_data.length ? ", " : "");
            });
            return clinicianNames;
        },
        getParentName() {
            return this.learnersList ? (this.learnersList[0].learner_data.parent_data ? this.learnersList[0].learner_data.parent_data.user.fullname : "") : "";
        },
        getClassName(serviceType) {
            let value = "";
            if (serviceType === "mini-artic-assessment") {
                value = "bg-primary";
            } else if (serviceType === "mini-literacy-diagnostic") {
                value = "bg-secondary";
            } else if (serviceType === "homework-helper") {
                value = "bg-warning";
            }
            console.log(serviceType, value)
            return value;
        }
    },
}
</script>

<style>
.bb-0 {
    border-bottom: 0;
}
</style>