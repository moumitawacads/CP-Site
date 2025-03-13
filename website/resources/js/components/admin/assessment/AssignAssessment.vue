<template>
    <div class="">
        <h2 class="mb-4">Assign Assessment</h2>
        <div class="alert alert-success" role="alert" v-if="message">
            <span>{{ message }}</span>
        </div>
        <form class="needs-validation" novalidate ref="assignAssessmentForm"
            @submit.prevent="update($event, this.$route.params.id)" v-if="assessmentData">
            <div class="form-group">
                <label for="name">Learner Name:</label>
                <input type="text" class="form-control" id="learner_name"
                    :value="assessmentData.learner_data ? assessmentData.learner_data.user.fullname : ''" disabled
                    required />
            </div>
            <div class="form-group">
                <label for="name">Parents Name:</label>
                <input type="text" class="form-control" id="learner_name"
                    :value="assessmentData.user_data ? assessmentData.user_data.fullname : ''" disabled required />
            </div>
            <div class="form-group">
                <label for="name">Clinician:</label>
                <select class="form-select" v-model="formData.clinician_id" required>
                    <option value="">Select Clinician</option>
                    <option v-for="clinician in cliniciansList" :key="clinician.id" :value="clinician.id">{{
                        clinician.first_name + " " + clinician.last_name }}</option>
                </select>
                <div class="invalid-feedback">{{ errors.clinician_id }}</div>
            </div>
            <div class="form-group mt-3">
                <button type="submit" class="btn btn-primary" :disabled="disabledBtn">
                    Assign
                </button>
                <router-link to="/admin/services/assessments" :disabled="disabledBtn" class="btn btn-secondary"
                    style="margin-left: 8px">Back</router-link>
            </div>
        </form>
    </div>
</template>

<script>
export default {
    name: "AssignAssessment",
    data() {
        return {
            formData: {
                clinician_id: "",
                learner_id: "",
                parents_id: "",
            },
            message: null,
            cliniciansList: null,
            assessmentData: null,
            disabledBtn: false,
            errorMsg: null,
            successMsg: null,
            errors: {
                clinician_id: "Clinician field is required.",
            },
        }
    },
    mounted() {
        this.getAllClinicians();
        this.getAssessmentData();
    },
    methods: {
        async getAllClinicians() {
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
        async getAssessmentData() {
            try {
                const response = await axios.get("/api/admin/assessment?id=" + this.$route.params.id, {
                    headers: {
                        Authorization: `Bearer ${this.$store.state.token}`,
                    },
                });
                this.assessmentData = response.data.data.length > 0 ? response.data.data[0] : response.data.data;
                if (Object.keys(this.assessmentData).length > 0) {
                    this.formData.learner_id = this.assessmentData.learner_data.id;
                    this.formData.parents_id = this.assessmentData.user_data.id;
                }
            } catch (error) {
                console.error(error);
            }
        },
        async update(e, id) {
            this.disabledBtn = true;
            let validation = this.checkForm(e);
            if (validation) {
                try {
                    const response = await axios.post(`/api/admin/clinician/${this.$route.params.id}/assessment-service`, this.formData, {
                        headers: {
                            Authorization: `Bearer ${this.$store.state.token}`,
                        },
                    });
                    this.message = response.data.message;
                    this.disabledBtn = false;
                    this.clearForm();
                } catch (error) {
                    this.errorMsg = error.response.data.message;
                    this.disabledBtn = false;
                    console.error("Login failed", error);
                }
            } else {
                this.disabledBtn = false;
            }
        },
        checkForm(e) {
            if (this.formData.clinician_id != "") return true;
            let form = document.querySelector(".needs-validation");
            form.classList.add("was-validated");
            e.preventDefault();
            return false;
        },
        clearForm() {
            this.formData = {
                clinician_id: "",
                learner_id: "",
                parents_id: "",
            };
            setTimeout(() => {
                this.$router.push("/admin/services/assessments");
            }, 2000);
        },
    }
}
</script>

<style></style>