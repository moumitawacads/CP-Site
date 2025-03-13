<template>
  <div class="d-flex justify-content-center">
    <div class="card px-4 py-4 col-md-8">
      <h2 class="text-3xl font-bold text-center mb-6">{{ $t("Add Learner") }}</h2>
      <form class="needs-validation" novalidate ref="addLearnerForm" @submit.prevent="handleSubmit">
        <div class="form-group mt-2">
          <label for="fullName">{{ $t("fullname") }}:</label>
          <input type="text" id="fullName" class="form-control" v-model="formData.fullname" required />
          <div class="invalid-feedback">{{ $t(errors.fullname) }}</div>
        </div>
        <div class="form-group mt-2" v-if="userData.user.role_slug === 'clinician'">
          <label for="clinician_name">{{ $t("clinician_name") }}:</label>
          <input type="text" id="clinician_name" class="form-control" v-model="formData.clinician_name" disabled />
        </div>
        <div class="form-group mt-2">
          <label for="gender">{{ $t("gender") }}:</label>
          <select class="form-select" v-model="formData.gender" required>
            <option value="">{{ $t("select_any") }}</option>
            <template v-for="(gender, indexG) in genders" :key="indexG + 1">
              <option :value="gender.value">{{ gender.value }}</option>
            </template>
          </select>
          <!-- <input
            type="text"
            id="gender"
            class="form-control"
            v-model="formData.gender"
            required
          /> -->
          <div class="invalid-feedback">{{ $t(errors.gender) }}</div>
        </div>
        <div class="form-group mt-2">
          <label for="learner_age">{{ $t("learner_age") }}:</label>
          <select class="form-select" v-model="formData.learner_age" required>
            <option value="">{{ $t("select_any") }}</option>
            <template v-for="(learnerAge, indexLA) in learnerAges" :key="indexLA + 1">
              <option :value="learnerAge.value">{{ learnerAge.value }}</option>
            </template>
          </select>
          <!-- <input
            type="text"
            id="learner_age"
            class="form-control"
            v-model="formData.learner_age"
            required
          /> -->
          <div class="invalid-feedback">{{ $t(errors.learner_age) }}</div>
        </div>
        <div class="form-group mt-2">
          <label for="name_of_school">{{ $t("name_of_school") }}:</label>
          <input type="text" id="name_of_school" class="form-control" v-model="formData.name_of_school" required />
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
          <!-- <input
            type="text"
            id="grade"
            class="form-control"
            v-model="formData.grade"
            required
          /> -->
          <div class="invalid-feedback">{{ $t(errors.grade) }}</div>
        </div>
        <div class="form-group mt-2">
          <label for="learner_email">{{ $t("learner_email") }}:</label>
          <input type="email" name="learnerEmailInput" id="learner_email" class="form-control"
            v-model="formData.learner_email" autocomplete="off" />
          <div class="invalid-feedback">{{ $t(errors.learner_email) }}</div>
        </div>
        <div class="form-group mt-2">
          <label for="learner_phone_number">{{ $t("learner_phone_number") }}:</label>
          <input type="text" id="learner_phone_number" class="form-control" v-model="formData.learner_phone_number" />
          <div class="invalid-feedback">{{ $t(errors.learner_phone_number) }}</div>
        </div>
        <div class="form-group mt-2">
          <label for="password">{{ $t("password") }}:</label>
          <input type="password" id="password" name="passwordInput" class="form-control" v-model="formData.password" />
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
          <input type="text" id="family_diagnosis" class="form-control" v-model="formData.family_diagnosis" required />
          <div class="invalid-feedback">{{ $t(errors.family_diagnosis) }}</div>
        </div>
        <div class="form-group mt-2">
          <label for="history">{{ $t("history") }}:</label>
          <input type="text" id="history" class="form-control" v-model="formData.history" required />
          <div class="invalid-feedback">{{ $t(errors.history) }}</div>
        </div>
        <div class="form-group mt-2">
          <label for="learner_type">{{ $t("learner_type") }}:</label>
          <input type="text" id="learner_type" class="form-control" v-model="formData.learner_type" required />
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
        <div class="mt-3">
          <button type="submit" class="btn btn-primary" :disabled="disabledBtn">
            {{ $t("submit") }}
          </button>
          <button type="button" class="btn btn-secondary" style="margin-left: 10px" @click="goBack"
            :disabled="disabledBtn">
            {{ $t("back") }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import axios from "../../axios";

export default {
  name: "AddLearner",
  props: ["userData"],
  data() {
    return {
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
      genders: null,
      learnerAges: null,
      grades: null,
      parentData: null,
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
  computed: {
    speechLanguageDiagnosisList() {
      return this.$store.state.speechLanguageDiagnosisList;
    },
  },
  mounted() {
    this.genders = this.$store.state.genders;
    this.learnerAges = this.$store.state.learnerAges;
    this.grades = this.$store.state.grades;
    if (this.userData.user.role_slug === "parents") {
      this.formData.parent_id = this.userData.id;
    } else {
      this.formData.clinician_id = this.userData.id;
      this.formData.clinician_name = this.userData.user.fullname;
    }
  },
  methods: {
    async handleSubmit(event) {
      event.preventDefault();
      this.disabledBtn = true;
      let validation = this.checkForm(event);
      if (validation) {
        try {
          const response = await axios.post(
            "/api/" +
            this.userData.user.role_slug +
            "/learner-registration-by-" +
            this.userData.user.role_slug,
            this.formData
          );
          if (response && response.status == 200) {
            // console.log(response.data);
            this.disabledBtn = false;
            this.goBack();
          }
        } catch (error) {
          if (
            error.response.data.status_code === 422 &&
            error.response.data.message === "Validation errors"
          ) {
            let form = document.querySelector(".needs-validation");
            form.classList.remove("was-validated");
            if (error.response.data.errors) {
              for (const [field, messages] of Object.entries(
                error.response.data.errors
              )) {
                if (document.getElementById(field))
                  document.getElementById(field).classList.add("is-invalid");
                this.errors[field] = messages[0];
              }
            }
          }
          this.disabledBtn = false;
          console.error("Errors:", this.errors);
        }
      } else {
        this.disabledBtn = false;
      }
    },
    goBack() {
      this.$refs.addLearnerForm.reset();
      this.$emit("goback");
    },
    checkForm(e) {
      if (
        this.formData.fullname != "" &&
        this.formData.gender != "" &&
        this.formData.learner_age != "" &&
        this.formData.name_of_school != "" &&
        this.formData.grade != "" &&
        this.formData.culture != "" &&
        this.formData.family_diagnosis != "" &&
        this.formData.history != "" &&
        this.formData.learner_type != "" &&
        this.formData.speech_language_diagnosis != "" &&
        this.formData.preferred_language != ""
      )
        return true;
      let form = document.querySelector(".needs-validation");
      form.classList.add("was-validated");
      e.preventDefault();
      return false;
    },
    validateEmail() {
      const email = this.formData.learner_email;
      if (!email) {
        document.getElementById("learner_email").classList.add("is-invalid");
        this.errors.learner_email = "Learner email field is required.";
      } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        document.getElementById("learner_email").classList.add("is-invalid");
        this.errors.learner_email = "Please enter a valid email address.";
      } else {
        document.getElementById("learner_email").classList.remove("is-invalid");
        this.errors.learner_email = "";
      }
    },
  },
  watch: {
    // Watch for changes in formData to revalidate dynamically
    // "formData.learner_email": "validateEmail",
  },
};
</script>

<style scoped>
h1 {
  color: #4caf50;
}
</style>
