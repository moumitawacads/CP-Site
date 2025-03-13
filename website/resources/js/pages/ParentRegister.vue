<template>
  <div class="container">
    <div class="row d-flex justify-content-center">
      <div class="col-md-6">
        <div class="card px-4 py-4">
          <h2 class="text-3xl font-bold text-center mb-6">
            {{ $t("parent_registration") }}
          </h2>
          <form class="needs-validation" novalidate ref="parentRegisterForm" @submit.prevent="handleSubmit">
            <div class="form-group mt-2">
              <label for="fullName">{{ $t("parent_or_guardian_name") }}:</label>
              <input type="text" id="fullName" class="form-control" v-model="formData.fullname" required />
              <div class="invalid-feedback">{{ $t(errors.fullname) }}</div>
            </div>
            <div class="form-group mt-2">
              <label for="type">{{ $t("i_am_a_parent_or_guardian") }}:</label>
              <select v-model="formData.type" @change="typeChange" required class="form-select">
                <option value="">{{ $t("select_any") }}</option>
                <option value="parent">{{ $t("parent") }}</option>
                <option value="guardian">{{ $t("guardian") }}</option>
                <option value="other">{{ $t("Other") }}</option>
              </select>
              <input type="text" class="form-control mt-2" v-model="typeTextbox" v-if="isVisible" />
              <div class="invalid-feedback">{{ $t(errors.type) }}</div>
            </div>
            <div class="form-group mt-2">
              <label for="reason">{{ $t("why_did_you_sign_up_for_chattiepant") }}?</label>
              <select v-model="formData.reason" required class="form-select">
                <option value="">{{ $t("select_any") }}</option>
                <option value="improve-speech-sounds">
                  {{ $t("improve_speech_sounds") }}
                </option>
                <option value="learn-to-read-better">
                  {{ $t("learn_to_read_better") }}
                </option>
                <option value="improve-language">{{ $t("improve_language") }}</option>
                <option value="improve-social-skills">
                  {{ $t("improve_social_skills") }}
                </option>
              </select>
              <div class="invalid-feedback">{{ $t(errors.reason) }}</div>
            </div>
            <div class="form-group mt-2">
              <label for="email">{{ $t("email") }}:</label>
              <input type="email" id="email" class="form-control" v-model="formData.email" required />
              <div class="invalid-feedback">{{ $t(errors.email) }}</div>
            </div>
            <div class="form-group mt-2">
              <label for="phone">{{ $t("phone_number") }}:</label>
              <input type="tel" id="phone_number" class="form-control" v-model="formData.phone_number" required />
              <div class="invalid-feedback">{{ $t(errors.phone_number) }}</div>
            </div>
            <div class="form-group mt-2">
              <label for="password">{{ $t("password") }}:</label>
              <input type="password" id="password" class="form-control" v-model="formData.password" required />
              <div class="invalid-feedback">{{ $t(errors.password) }}</div>
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
              <button type="submit" class="btn btn-primary" :disable="disabledBtn">
                {{ $t("submit") }}
              </button>
              <button type="button" class="btn btn-secondary" style="margin-left: 10px" :disable="disabledBtn"
                @click="goBack">
                {{ $t("back") }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "../axios";

export default {
  name: "ParentRegister",
  data() {
    return {
      formData: {
        fullname: "",
        type: "",
        reason: "",
        email: "",
        phone_number: "",
        password: "",
        preferred_language: "",
      },
      isVisible: false,
      typeTextbox: null,
      disabledBtn: false,
      errors: {
        fullname: "Parent/Guardian name field is required.",
        type: "Type field is required.",
        reason: "Reason field is required.",
        email: "Email field is required.",
        phone_number: "Phone number field is required.",
        password: "Password field is required.",
        preferred_language: "Preferred language field is required.",
      },
    };
  },
  mounted() {
    if (this.$ls.get("cart")) {
      // console.log('parent')
    } else {
      this.$router.push({ name: "home" });
    }
  },
  methods: {
    async handleSubmit(event) {
      event.preventDefault();
      this.disabledBtn = true;
      let validation = this.checkForm(event);
      if (validation) {
        try {
          if (this.formData.type === "other") {
            this.formData.type = this.typeTextbox;
          }
          const response = await axios.post("/api/parents/validation", this.formData);
          if (response && response.status == 200) {
            this.disabledBtn = false;

            this.$ls.set("userData", this.formData);
            this.$ls.set("userType", "parent");
            this.$router.push("/shopping-cart");

            // this.$ls.set("parent_data", response.data.data);
            // this.$router.push("/learner-register-by-parent");
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
        this.formData.type != "" &&
        this.formData.reason != "" &&
        this.formData.email != "" &&
        this.formData.phone_number != "" &&
        this.formData.password != "" &&
        this.formData.preferred_language != ""
      )
        return true;
      let form = document.querySelector(".needs-validation");
      form.classList.add("was-validated");
      e.preventDefault();
      return false;
    },
    typeChange(e) {
      if (e.target.value == "other") {
        this.isVisible = true;
      } else {
        this.isVisible = false;
        this.typeTextbox = null;
        this.formData.type = e.target.value;
      }
    },
    goBack() {
      this.$ls.remove("cart");
      this.$router.push("/");
    },
  },
};
</script>

<style scoped>
h1 {
  color: #4caf50;
}
</style>
