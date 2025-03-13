<template>
  <div class="col-md-6">
    <div class="card px-4 py-4">
      <h2 class="text-3xl font-bold text-center mb-6">
        {{ $t("learner_registration_by_clinician") }}
      </h2>
      <form class="needs-validation" novalidate ref="learnerRegistrationByClinicianForm" @submit.prevent="handleSubmit">
        <div class="form-group mt-2">
          <label for="fullName">{{ $t("fullname") }}:</label>
          <input type="text" class="form-control" id="fullName" v-model="formData.fullname" required />
          <div class="invalid-feedback">{{ $t(errors.fullname) }}</div>
        </div>
        <div class="form-group mt-2">
          <label for="clinician_name">{{ $t("clinician_name") }}:</label>
          <input type="text" id="clinician_name" class="form-control" v-model="formData.clinician_name" disabled />
        </div>
        <div class="form-group mt-2">
          <label for="gender">{{ $t("gender") }}:</label>
          <input type="text" id="gender" class="form-control" v-model="formData.gender" required />
          <div class="invalid-feedback">{{ $t(errors.gender) }}</div>
        </div>
        <div class="form-group mt-2">
          <label for="name_of_school">{{ $t("name_of_school") }}:</label>
          <input type="text" id="name_of_school" class="form-control" v-model="formData.name_of_school" required />
          <div class="invalid-feedback">{{ $t(errors.name_of_school) }}</div>
        </div>
        <div class="form-group mt-2">
          <label for="learner_age">{{ $t("learner_age") }}:</label>
          <input type="text" id="learner_age" class="form-control" v-model="formData.learner_age" required />
          <div class="invalid-feedback">{{ $t(errors.learner_age) }}</div>
        </div>
        <div class="form-group mt-2">
          <label for="grade">{{ $t("grade") }}:</label>
          <input type="text" id="grade" class="form-control" v-model="formData.grade" required />
          <div class="invalid-feedback">{{ $t(errors.grade) }}</div>
        </div>
        <div class="form-group mt-2">
          <label for="learner_email">{{ $t("learner_email") }}:</label>
          <input type="text" id="learner_email" class="form-control" v-model="formData.learner_email" autocomplete="off"
            required />
          <div class="invalid-feedback">{{ $t(errors.learner_email) }}</div>
        </div>
        <div class="form-group mt-2">
          <label for="learner_phone_number">{{ $t("learner_phone_number") }}:</label>
          <input type="text" id="learner_phone_number" class="form-control" v-model="formData.learner_phone_number"
            required />
          <div class="invalid-feedback">{{ $t(errors.learner_phone_number) }}</div>
        </div>
        <div class="form-group mt-2">
          <label for="password">{{ $t("password") }}:</label>
          <input type="password" id="password" class="form-control" v-model="formData.password" required />
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
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import axios from "../axios";

export default {
  name: "LearnerRegisterByClinician",
  data() {
    return {
      formData: {
        clinician_id: "",
        fullname: "",
        clinician_name: "",
        gender: "",
        name_of_school: "",
        learner_age: "",
        grade: "",
        learner_email: "",
        learner_phone_number: "",
        password: "",
        culture: "",
        family_diagnosis: "",
        history: "",
        learner_type: "",
        speech_language_diagnosis: "",
        preferred_language: "",
      },
      speechLanguageDiagnosisList: [],
      clinicianData: null,
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
    this.getSpeechLanguageDiagnosisList();
    this.clinicianData = this.$ls.get("clinician_data");
    this.formData.clinician_name =
      this.clinicianData.first_name + " " + this.clinicianData.last_name;
    this.formData.clinician_id = this.clinicianData.id;
  },
  methods: {
    async handleSubmit(event) {
      event.preventDefault();
      this.disabledBtn = true;
      let validation = this.checkForm(event);
      if (validation) {
        try {
          const response = await axios.post(
            "/api/clinician/learner-registration-by-clinician",
            this.formData
          );
          // console.log(response.data);
          if (response && response.status == 200) {
            this.disabledBtn = false;

            this.$store.dispatch("login", response.data);
            this.$router.push("/dashboard");
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
          console.error("Error uploading file:", error);
        }
      } else {
        this.disabledBtn = false;
      }
    },
    checkForm(e) {
      if (
        this.formData.fullname != "" &&
        this.formData.gender != "" &&
        this.formData.learner_age != "" &&
        this.formData.name_of_school != "" &&
        this.formData.grade != "" &&
        this.formData.culture != "" &&
        this.formData.learner_email != "" &&
        this.formData.learner_phone_number != "" &&
        this.formData.password != "" &&
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
    async getSpeechLanguageDiagnosisList() {
      const response = await axios.get("/api/speech-language-diagnosis-list");
      this.speechLanguageDiagnosisList = response.data.data;
    },
  },
};
</script>

<style scoped>
h1 {
  color: #4caf50;
}

.container {
  max-width: 600px;
  border: 2px solid rgb(0, 0, 0);
  border-radius: 8px;
  padding: 20px;
}

.form-input {
  transition: border-color 0.3s ease;
  border: 1px solid #ccc;
  border-radius: 4px;
  padding: 8px;
  width: 100%;
  margin-bottom: 10px;
}

.form-input:focus {
  border-color: #4f46e5;
  outline: none;
}
</style>
