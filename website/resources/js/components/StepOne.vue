<template>
    <div class="card">
        <div class="card-body">
            <div class="row nav justify-content-center">
                <div class="col-3">
                    <select class="form-select">
                        <option value="">Select Learner</option>
                        <option v-for="learner in learnersData" :key="learner.learner_data.id"
                            :value="learner.learner_data.id">
                            {{ learner.learner_data.user.fullname }} - {{ learner.plan_name }}
                        </option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "StepOne",
    props: [],
    data() {
        return {
            form: {
                username: "",
                demoEmail: "",
                message: "",
            },
            learnersData: null,
        };
    },
    mounted() {
        this.getServiceLearnerByParentId();
    },
    methods: {
        async getServiceLearnerByParentId() {
            try {
                const response = await axios.get(
                    "/api/parents/" + this.$store.state.providerData.id + "/service-learners",
                    {
                        headers: {
                            Authorization: "Bearer " + this.$store.state.token,
                        },
                    }
                );
                if (response && response.status == 200) {
                    this.learnersData = response.data.data;
                    // console.log(this.learnersData);
                }
            } catch (error) {
                console.error("Error uploading file:", error);
            }
        },
    },
};
</script>

<style></style>
