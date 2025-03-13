<template>
  <div class="col-md-6">
    <div class="card px-4 py-4">
      <h2 class="text-3xl font-bold text-center mb-6">
        {{ $t("Clinician Learner Link") }}
      </h2>
      <form class="needs-validation" novalidate ref="clinicianLearnerLinkForm" @submit.prevent="verify">
        <div class="form-group mt-2">
          <label for="password">{{ $t("clinician_name") }}:</label>
          <input v-model="formData.clinician_name" type="text" class="form-control"
            :placeholder="$t('enter_name_placeholder')" required />
          <div class="invalid-feedback">{{ $t(errors.clinician_name) }}</div>
        </div>
        <div class="form-group mt-2">
          <label for="password">{{ $t("clinician_code") }}:</label>
          <input v-model="formData.clinician_code" type="text" class="form-control"
            :placeholder="$t('enter_code_placeholder')" required />
          <div class="invalid-feedback">{{ $t(errors.clinician_code) }}</div>
        </div>
        <div class="mt-3">
          <button type="submit" class="btn btn-primary">{{ $t("submit") }}</button>
          <button type="button" class="btn btn-secondary" style="margin-left: 10px" @click="skipLink">
            {{ $t("skip") }}
          </button>
        </div>
        <span class="err" v-if="errorMsg">{{ errorMsg }}</span>
      </form>
    </div>
  </div>
</template>

<script>
import axios from "../axios";
export default {
  data() {
    return {
      formData: {
        learner_id: "",
        learner_encrypt_data_id: "",
        clinician_name: "",
        clinician_code: "",
      },
      disabledBtn: false,
      errorMsg: null,
      userData: null,
      wholeData: null,
      errors: {
        clinician_name: "Clinician Name field is required.",
        clinician_code: "Clinician Code field is required.",
      },
    };
  },
  mounted() {
    this.userData = this.$store.state.user;
    this.wholeData = this.$ls.get("whole_data");
  },
  methods: {
    async verify() {
      this.disabledBtn = true;
      let validation = this.checkForm(event);
      if (validation) {
        try {
          this.errorMsg = null;
          this.formData.learner_id = this.$ls.get("learner_id");
          this.formData.learner_encrypt_data_id = this.$ls.get("learner_encrypt_data_id");

          const response = await axios.post(
            "/api/parents/clinician-learner-link",
            this.formData,
            {
              headers: {
                Authorization: "Bearer " + this.$store.state.token,
              },
            }
          );
          if (response && response.status == 200) {
            this.disabledBtn = false;
            // set preferred language
            this.$store.dispatch("login", this.wholeData);
            this.$i18n.locale = this.wholeData.user.preferred_language;
            this.$router.push({ name: "dashboard" });
          }
        } catch (error) {
          this.disabledBtn = false;
          this.errorMsg = error.response.data.message;
        }
      } else {
        this.disabledBtn = false;
      }
    },
    checkForm(e) {
      if (this.formData.clinician_name != "" && this.formData.clinician_code != "")
        return true;
      let form = document.querySelector(".needs-validation");
      form.classList.add("was-validated");
      e.preventDefault();
      return false;
    },
    skipLink() {
      // set preferred language
      this.$store.dispatch("login", this.wholeData);
      this.$i18n.locale = this.wholeData.user.preferred_language;
      this.$router.push({ name: "dashboard" });
    },
  },
};
</script>

<style scoped>
h1 {
  color: #4caf50;
}

.err {
  color: red;
}
</style>
