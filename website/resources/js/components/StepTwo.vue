<template>
    <div class="card">
        <div class="card-body" v-if="userData">
            <form class="needs-validation" novalidate ref="addLearnerForm" @submit.prevent="handleSubmit">
                <div class="form-group mt-2">
                    <label for="gender">{{ $t("gender") }}:</label>
                    <select class="form-select" v-model="formData.gender" required>
                        <option value="">{{ $t("select_any") }}</option>
                        <template v-for="(gender, indexG) in genders" :key="indexG + 1">
                            <option :value="gender.value">{{ gender.value }}</option>
                        </template>
                    </select>
                    <div class="invalid-feedback">{{ $t(errors.gender) }}</div>
                </div>
                <div class="form-group mt-2">
                    <label for="name_of_school">{{ $t("name_of_school") }}:</label>
                    <input type="text" id="name_of_school" class="form-control" v-model="formData.name_of_school"
                        required />
                    <div class="invalid-feedback">{{ $t(errors.name_of_school) }}</div>
                </div>
                <div class="form-group mt-2">
                    <label for="grade">{{ $t("grade") }}:</label>
                    <select class="form-select" v-model="formData.grade" required>
                        <option value="">{{ $t("select_any") }}</option>
                        <template v-for="(grade, indexgr) in grades" :key="indexgr + 1">
                            <option :value="grade.value">{{ grade.value }}</option>
                        </template>
                    </select>
                    <div class="invalid-feedback">{{ $t(errors.grade) }}</div>
                </div>
                <div class="form-group mt-2">
                    <label for="learner_email">{{ $t("learner_email") }}:</label>
                    <input type="email" name="learnerEmailInput" id="learner_email" class="form-control"
                        v-model="formData.learner_email" required autocomplete="off" />
                    <div class="invalid-feedback">{{ $t(errors.learner_email) }}</div>
                </div>
                <div class="form-group mt-2">
                    <label for="learner_phone_number">{{ $t("learner_phone_number") }}:</label>
                    <input type="text" id="learner_phone_number" class="form-control"
                        v-model="formData.learner_phone_number" required />
                    <div class="invalid-feedback">{{ $t(errors.learner_phone_number) }}</div>
                </div>
                <div class="form-group mt-2">
                    <label for="password">{{ $t("password") }}:</label>
                    <input type="password" id="password" name="passwordInput" class="form-control"
                        v-model="formData.password" required />
                    <div class="invalid-feedback">{{ $t(errors.password) }}</div>
                </div>
                <div class="form-group mt-2">
                    <label for="city">{{ $t("culture") }}:</label>
                    <select v-model="formData.culture" required class="form-select">
                        <option value="">{{ $t("select_any") }}</option>
                        <option value="Franophone">{{ $t("franophone") }}</option>
                        <option value="Indigenous">{{ $t("indigenous") }}</option>
                        <option value="Immigrant">{{ $t("immigrant") }}</option>
                    </select>
                    <div class="invalid-feedback">{{ $t(errors.culture) }}</div>
                </div>
                <div class="form-group mt-2">
                    <label for="family_diagnosis">{{ $t("family_diagnosis") }}:</label>
                    <input type="text" id="family_diagnosis" class="form-control" v-model="formData.family_diagnosis"
                        required />
                    <div class="invalid-feedback">{{ $t(errors.family_diagnosis) }}</div>
                </div>
                <div class="form-group mt-2">
                    <label for="history">{{ $t("history") }}:</label>
                    <input type="text" id="history" class="form-control" v-model="formData.history" required />
                    <div class="invalid-feedback">{{ $t(errors.history) }}</div>
                </div>
                <div class="form-group mt-2">
                    <label for="learner_type">{{ $t("learner_type") }}:</label>
                    <input type="text" id="learner_type" class="form-control" v-model="formData.learner_type"
                        required />
                    <div class="invalid-feedback">{{ $t(errors.learner_type) }}</div>
                </div>
                <div class="form-group mt-2">
                    <label for="speech_language_diagnosis">{{ $t("speech_language_diagnosis") }}:</label>
                    <select v-model="formData.speech_language_diagnosis" required class="form-select">
                        <option value="">{{ $t("select_any") }}</option>
                        <option v-for="(speechLanguageDiagnosis, index) in speechLanguageDiagnosisList" :key="index + 1"
                            :value="speechLanguageDiagnosis.slug">
                            {{ $t(speechLanguageDiagnosis.name) }}
                        </option>
                    </select>
                    <div class="invalid-feedback">{{ $t(errors.speech_language_diagnosis) }}</div>
                </div>
                <div class="form-group mt-2">
                    <label for="preferred_language">{{ $t("preferred_language") }}:</label>
                    <select v-model="formData.preferred_language" required class="form-select">
                        <option value="">{{ $t("select_any") }}</option>
                        <option value="en">English</option>
                        <option value="fr">French</option>
                    </select>
                    <div class="invalid-feedback">{{ $t(errors.preferred_language) }}</div>
                </div>
                <!-- <div class="mt-3">
                    <button type="submit" class="btn btn-primary" :disabled="disabledBtn">
                        {{ $t("submit") }}
                    </button>
                    <button type="button" class="btn btn-secondary" style="margin-left: 10px" @click="goBack"
                        :disabled="disabledBtn">
                        {{ $t("back") }}
                    </button>
                </div> -->
            </form>
        </div>
    </div>
</template>

<script>
export default {
    name: "StepTwo",
    data() {
        return {
            userData: null,
            formData: {
                fullname: "",
                gender: "",
                learner_age: "",
                name_of_school: "",
                grade: "",
                learner_email: "",
                learner_phone_number: "",
                password: "",
                culture: "",
                family_diagnosis: "",
                history: "",
                learner_type: "",
                speech_language_diagnosis: "",
                tab_id: "my-learners",
                preferred_language: "",
            },
            disabledBtn: false,
            errors: {
                fullname: "Fullname field is required.",
                gender: "Gender field is required.",
                learner_age: "Learner age field is required.",
                name_of_school: "Name of school field is required.",
                grade: "Grade field is required.",
                learner_email: "Learner email field is required.",
                learner_phone_number: "Learner phone number field is required.",
                password: "Password field is required.",
                culture: "Culture field is required.",
                family_diagnosis: "Family diagnosis field is required.",
                history: "History field is required.",
                learner_type: "Learner type field is required.",
                speech_language_diagnosis: "Speech language diagnosis field is required.",
                preferred_language: "Preferred language field is required.",
            },
        };
    },
    mounted() {
        this.userData = this.$store.state.providerData;
        console.log(this.userData);
    },
};
</script>

<style></style>
