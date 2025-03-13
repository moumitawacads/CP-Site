<template>
    <div class="">
        <h2 class="mb-4">Clinicians</h2>
        <div class="row">
            <div class="col-md-3">
                <input type="text" class="form-control mb-3" placeholder="Search..." v-model="searchQuery" />
            </div>
            <div class="col-md-4"></div>
            <div class="col-md-2"></div>
            <div class="col-md-2 d-flex justify-content-end">

            </div>
        </div>

        <table class="table table-bordered" v-if="cliniciansList">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fullname</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Number of Learners</th>
                    <th style="width: 130px">Actions</th>
                </tr>
            </thead>
            <tbody>
                <template v-if="filteredClinicianList.length === 0">
                    <tr>
                        <td colspan="7" class="text-center">No data found</td>
                    </tr>
                </template>
                <template v-else>
                    <tr v-for="(clinician, index) in filteredClinicianList" :key="clinician.id">
                        <td>{{ index + 1 }}</td>
                        <td>{{ clinician.user.fullname }}</td>
                        <td>{{ clinician.user.email }}</td>
                        <td>{{ clinician.user.phone_number }}</td>
                        <td>{{ clinician.learners_count }}</td>
                        <td>
                            <button class="btn btn-primary btn-sm" v-if="clinician.learners_count > 0"
                                @click="viewLearners(clinician.id)">
                                View
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
    name: "Clinician",
    data() {
        return {
            cliniciansList: null,
            searchQuery: "",
        };
    },
    computed: {
        filteredClinicianList() {
            return this.cliniciansList
                ? this.cliniciansList.filter((item) => {
                    const matchesSearchQuery = item.user.fullname
                        .toLowerCase()
                        .includes(this.searchQuery.toLowerCase());
                    return matchesSearchQuery;
                })
                : [];
        },
    },
    mounted() {
        this.getAllClinicianList();
    },
    methods: {
        async getAllClinicianList() {
            try {
                const response = await axios.get("/api/admin/clinician", {
                    headers: {
                        Authorization: `Bearer ${this.$store.state.token}`,
                    },
                });
                this.cliniciansList = response.data.data;
            } catch (error) {
                console.error(error);
            }
        },
        viewLearners(clinicianId) {
            this.$router.push({ name: "view-clinician-learners", params: { id: clinicianId } });
        }
    },
}
</script>

<style></style>