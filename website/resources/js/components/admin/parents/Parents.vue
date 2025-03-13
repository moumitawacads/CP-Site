<template>
    <div class="">
        <h2 class="mb-4">Parents</h2>
        <div class="row">
            <div class="col-md-3">
                <input type="text" class="form-control mb-3" placeholder="Search..." v-model="searchQuery" />
            </div>
            <div class="col-md-4"></div>
            <div class="col-md-2"></div>
            <div class="col-md-2 d-flex justify-content-end">

            </div>
        </div>

        <table class="table table-bordered" v-if="parentsList">
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
                <template v-if="filteredParentsList.length === 0">
                    <tr>
                        <td colspan="7" class="text-center">No data found</td>
                    </tr>
                </template>
                <template v-else>
                    <tr v-for="(parent, index) in filteredParentsList" :key="parent.id">
                        <td>{{ index + 1 }}</td>
                        <td>{{ parent.user.fullname }}</td>
                        <td>{{ parent.user.email }}</td>
                        <td>{{ parent.user.phone_number }}</td>
                        <td>{{ parent.learners_count }}</td>
                        <td>
                            <button class="btn btn-primary btn-sm" v-if="parent.learners_count > 0"
                                @click="viewLearners(parent.id)">
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
    name: "Parents",
    data() {
        return {
            parentsList: null,
            searchQuery: "",
        };
    },
    computed: {
        filteredParentsList() {
            return this.parentsList
                ? this.parentsList.filter((item) => {
                    const matchesSearchQuery = item.user.fullname
                        .toLowerCase()
                        .includes(this.searchQuery.toLowerCase());
                    return matchesSearchQuery;
                })
                : [];
        },
    },
    mounted() {
        this.getAllParentsList();
    },
    methods: {
        async getAllParentsList() {
            try {
                const response = await axios.get("/api/admin/parents", {
                    headers: {
                        Authorization: `Bearer ${this.$store.state.token}`,
                    },
                });
                this.parentsList = response.data.data;
            } catch (error) {
                console.error(error);
            }
        },
        viewLearners(parentId) {
            this.$router.push({ name: "view-parents-learners", params: { id: parentId } });
        }
    },
}
</script>

<style></style>