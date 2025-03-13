<template>
  <div class="d-flex justify-content-center">
    <div class="card px-4 py-4 col-md-8">
      <h2 class="text-3xl font-bold text-center mb-6">{{ $t("Edit Learner") }}</h2>
      <form class="needs-validation" novalidate ref="editLearnerForm" @submit.prevent="handleSubmit">
        <div class="form-group mt-2">
          <label for="gender">{{ $t("gender") }}:</label>
          <input type="text" id="gender" class="form-control" v-model="formData.gender" required />
          <div class="invalid-feedback">{{ $t(errors.gender) }}</div>
        </div>
        <div class="form-group mt-2">
          <label for="grade">{{ $t("grade") }}:</label>
          <input type="text" id="grade" class="form-control" v-model="formData.grade" required />
          <div class="invalid-feedback">{{ $t(errors.grade) }}</div>
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
export default {
  name: "EditLearner",
  props: ["learnerData", "parent_id"],
  data() {
    return {
      formData: {
        learner_id: "",
        learner_encrypt_data_id: "",
        gender: "",
        grade: "",
        culture: "",
        speech_language_diagnosis: "",
        tab_id: "my-learners",
        parent_id: "",
      },
      parentData: null,
      disabledBtn: false,
      errors: {
        gender: "Gender field is required.",
        grade: "Grade field is required.",
        culture: "Culture field is required.",
        speech_language_diagnosis: "Speech language diagnosis field is required.",
      },
    };
  },
  computed: {
    speechLanguageDiagnosisList() {
      return this.$store.state.speechLanguageDiagnosisList;
    },
  },
  mounted() {
    this.editLearnerFormFillUp();
  },
  methods: {
    async handleSubmit(event) {
      event.preventDefault();
      this.disabledBtn = true;
      let validation = this.checkForm(event);
      if (validation) {
        try {
          const response = await axios.post("/api/learner/update", this.formData, {
            headers: {
              Authorization: "Bearer " + this.$store.state.token,
            },
          });
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
    editLearnerFormFillUp() {
      this.formData.parent_id = this.parent_id;
      this.formData.learner_id = this.learnerData.learner_data.id;
      this.formData.learner_encrypt_data_id = this.learnerData.learner_encrypt_data.id;
      this.formData.gender = this.learnerData.learner_data.gender;
      this.formData.grade = this.learnerData.learner_data.grade;
      this.formData.culture = this.learnerData.learner_encrypt_data.culture;
      this.formData.speech_language_diagnosis = this.learnerData.learner_encrypt_data.speech_language_diagnosis;
    },
    goBack() {
      this.$refs.editLearnerForm.reset();
      this.$emit("goback");
    },
    checkForm(e) {
      if (
        this.formData.gender != "" &&
        this.formData.grade != "" &&
        this.formData.culture != "" &&
        this.formData.speech_language_diagnosis != ""
      )
        return true;
      let form = document.querySelector(".needs-validation");
      form.classList.add("was-validated");
      e.preventDefault();
      return false;
    },
  },
};
</script>

<style></style>
