<template>
  <div class="container">
    <div class="row d-flex justify-content-center">
      <div class="col-md-6">
        <div class="card px-4 py-4">
          <h2 class="text-3xl font-bold text-center mb-6">
            {{ $t("clinician_registration") }}
          </h2>
          <form class="needs-validation" novalidate ref="clinicianRegisterForm" @submit.prevent="handleSubmit">
            <div class="form-group mt-2">
              <label for="fullName">{{ $t("fullname") }}:</label>
              <input type="text" class="form-control" v-model="formData.fullname" id="fullName"
                :placeholder="$t('enter_fullname_placeholder')" required />
              <div class="invalid-feedback">{{ $t(errors.fullname) }}</div>
            </div>
            <div class="form-group mt-2">
              <label for="address1">{{ $t("address_1") }}:</label>
              <input type="text" class="form-control" v-model="formData.address_1" id="address1"
                :placeholder="$t('enter_address_1_placeholder')" required />
              <div class="invalid-feedback">{{ $t(errors.address_1) }}</div>
            </div>
            <div class="form-group mt-2">
              <label for="address2">{{ $t("address_2") }}:</label>
              <input type="text" class="form-control" v-model="formData.address_2" id="address2"
                :placeholder="$t('enter_address_2_placeholder')" />
            </div>
            <div class="form-group mt-2">
              <label for="email">{{ $t("email") }}:</label>
              <input type="email" class="form-control" v-model="formData.email" id="email"
                :placeholder="$t('enter_email_placeholder')" required />
              <div class="invalid-feedback">{{ $t(errors.email) }}</div>
            </div>
            <div class="form-group mt-2">
              <label for="phone_number">{{ $t("phone_number") }}:</label>
              <input type="tel" class="form-control" v-model="formData.phone_number" id="phone_number"
                :placeholder="$t('enter_phone_number_placeholder')" required />
              <div class="invalid-feedback">{{ $t(errors.phone_number) }}</div>
            </div>
            <div class="form-group mt-2">
              <label for="password">{{ $t("password") }}:</label>
              <input type="password" class="form-control" v-model="formData.password" id="password"
                :placeholder="$t('enter_password_placeholder')" required />
              <div class="invalid-feedback">{{ $t(errors.password) }}</div>
            </div>
            <div class="form-group mt-2">
              <label for="city">{{ $t("city") }}:</label>
              <input type="text" class="form-control" v-model="formData.city" id="city"
                :placeholder="$t('enter_city_placeholder')" required />
              <div class="invalid-feedback">{{ $t(errors.city) }}</div>
            </div>
            <div class="form-group mt-2">
              <label for="province">{{ $t("province") }}:</label>
              <input type="text" class="form-control" v-model="formData.province" id="province"
                :placeholder="$t('enter_province_placeholder')" required />
              <div class="invalid-feedback">{{ $t(errors.province) }}</div>
            </div>
            <div class="form-group mt-2">
              <label for="postal_code">{{ $t("postal_code") }}:</label>
              <input type="text" class="form-control" v-model="formData.postal_code" id="postal_code"
                :placeholder="$t('enter_postal_code_placeholder')" required />
              <div class="invalid-feedback">{{ $t(errors.postal_code) }}</div>
            </div>
            <div class="form-group mt-2">
              <label for="country">{{ $t("country") }}:</label>
              <input type="text" class="form-control" v-model="formData.country" id="country"
                :placeholder="$t('enter_country_placeholder')" required />
              <div class="invalid-feedback">{{ $t(errors.country) }}</div>
            </div>
            <div class="form-group mt-2">
              <label for="occupation">{{ $t("occupation") }}:</label>
              <select v-model="formData.occupation_id" required class="form-select">
                <option value="">{{ $t("select_any") }}</option>
                <option v-for="(occupation, index) in occupationList" :key="index + 1" :value="occupation.id">
                  {{ $t(occupation.name) }}
                </option>
              </select>
              <div class="invalid-feedback">{{ $t(errors.occupation_id) }}</div>
            </div>
            <div class="form-group mt-2">
              <label for="certification">{{ $t("upload_clinician_certificate") }}:</label>
              <input class="form-control" type="file" id="certification" required :title="$t('choose_file')"
                @change="handleFileUpload" />
              <div class="invalid-feedback">
                {{ $t(errors.upload_clinician_certificate) }}
              </div>
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
              <button type="button" class="btn btn-secondary" style="margin-left: 10px" :disabled="disabledBtn"
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
  name: "ClinicianRegister",
  data() {
    return {
      formData: {
        fullname: "",
        address_1: "",
        address_2: "",
        email: "",
        phone_number: "",
        password: "",
        city: "",
        province: "",
        postal_code: "",
        country: "",
        occupation_id: "",
        upload_clinician_certificate: null,
        preferred_language: "",
      },
      occupationList: [],
      disabledBtn: false,
      errors: {
        fullname: "Fullname field is required.",
        address_1: "Address 1 field is required.",
        email: "Email field is required.",
        phone_number: "Phone number field is required.",
        password: "Password field is required.",
        city: "City field is required.",
        province: "Province field is required.",
        postal_code: "Postal Code field is required.",
        country: "Country field is required.",
        occupation_id: "Occupation field is required.",
        upload_clinician_certificate: "Clinician certificate field is required.",
        preferred_language: "Preferred language field is required.",
      },
    };
  },
  mounted() {
    if (this.$ls.get("cart")) {
      this.getOccupationsList();
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
          const response = await axios.post("/api/clinician/validation", this.formData, {
            headers: {
              "Content-Type": "multipart/form-data",
            },
          });
          if (response && response.status == 200) {
            this.disabledBtn = false;

            this.$ls.set("userData", this.formData);
            this.$ls.set("userType", "clinician");
            this.$router.push("/shopping-cart");

            // this.$ls.set("clinician_data", response.data.data);
            // this.$router.push("/learner-register-by-clinician");
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
                if (document.getElementById(field)) {
                  document.getElementById(field).classList.add("is-invalid");
                }
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
    handleFileUpload(event) {
      const file = event.target.files[0];
      this.formData.upload_clinician_certificate = file; // Store the selected file
      if (file) {
        const reader = new FileReader();
        reader.onload = () => {
          // Create an object with the file name and base64 data
          const fileData = {
            name: file.name, // Original file name
            data: reader.result, // Base64 file data
          };
          // Store the object as a JSON string in localStorage
          this.$ls.set("upload_clinician_certificate", fileData);
        };
        reader.readAsDataURL(file);
      }
    },
    async getOccupationsList() {
      const response = await axios.get("/api/occupation-list");
      this.occupationList = response.data.data;
    },
    checkForm(e) {
      if (
        this.formData.fullname != "" &&
        this.formData.address_1 != "" &&
        this.formData.email != "" &&
        this.formData.phone_number != "" &&
        this.formData.password != "" &&
        this.formData.city != "" &&
        this.formData.province != "" &&
        this.formData.postal_code != "" &&
        this.formData.country != "" &&
        this.formData.occupation_id != "" &&
        this.formData.upload_clinician_certificate != "" &&
        this.formData.preferred_language != ""
      )
        return true;
      let form = document.querySelector(".needs-validation");
      form.classList.add("was-validated");
      e.preventDefault();
      return false;
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
