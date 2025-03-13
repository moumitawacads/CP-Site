<template>
  <div class="container">
    <div class="row d-flex justify-content-center">
      <div class="col-md-6">
        <div class="card px-4 py-4">
          <h2 class="text-3xl font-bold text-center mb-6">
            {{ this.$route.name === "admin-login" ? "Admin Login" : $t("login") }}
          </h2>
          <form class="needs-validation" novalidate ref="loginForm" @submit.prevent="
            this.$route.name === 'admin-login' ? adminLogin() : login($event)
            ">
            <div class="form-group">
              <label for="exampleInputEmail1">{{ $t("username") }}</label>
              <input type="text" class="form-control" v-model="formData.username" id="username"
                :placeholder="$t('enter_email_phone_placeholder')" required />
              <div class="invalid-feedback">{{ $t(errors.username) }}</div>
            </div>
            <div class="form-group">
              <label for="exampleInputPassword1">{{ $t("password") }}</label>
              <input type="password" v-model="formData.password" class="form-control" id="password"
                :placeholder="$t('enter_password_placeholder')" required />
              <div class="invalid-feedback">{{ $t(errors.password) }}</div>
            </div>
            <!-- <div class="form-group form-check">
        <input type="checkbox" class="form-check-input" id="exampleCheck1" />
        <label class="form-check-label" for="exampleCheck1">Check me out</label>
      </div> -->
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
          <span :class="errorMsg
            ? 'd-flex mt-2 err'
            : 'd-flex mt-2' || successMsg
              ? 'd-flex mt-2 success'
              : ''
            " v-if="errorMsg || successMsg">{{
              this.$route.name === "admin-login"
                ? errorMsg || successMsg
                : errorMsg || successMsg
            }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import moment from "moment";
import axios from "../axios";
export default {
  data() {
    return {
      formData: {
        username: "",
        password: "",
      },
      disabledBtn: false,
      errorMsg: null,
      successMsg: null,
      errors: {
        username: "Username field is required.",
        password: "Password field is required.",
      },
    };
  },
  mounted() {
    // console.log(this.$route.name);
  },
  methods: {
    async login(event) {
      event.preventDefault();
      this.disabledBtn = true;
      let validation = this.checkForm(event);
      if (validation) {
        try {
          const response = await axios.post("/api/login", this.formData);
          if (response && response.status == 200) {
            this.disabledBtn = false;
            this.errorMsg = null;
            this.successMsg = response.data.message;
            this.$ls.set("user", response.data.user);
            setTimeout(() => {
              this.$router.push({ name: "verification" }); // Adjust route as per your setup
            }, 2000);
          }
        } catch (error) {
          this.errorMsg = error.response.data.message;
          this.disabledBtn = false;
          console.error("Login failed", error);
        }
      } else {
        this.disabledBtn = false;
      }
    },
    async adminLogin() {
      try {
        const response = await axios.post("/api/admin-login", this.formData);
        if (response && response.status == 200) {
          this.disabledBtn = false;
          this.errorMsg = null;
          this.successMsg = response.data.message;
          this.setToken(response.data);
          this.$router.push({ name: "admin-dashboard" }); // Adjust route as per your setup
        }
      } catch (error) {
        // console.log(error);
        this.errorMsg = error.response.data.message;
        this.disabledBtn = false;
        console.error("Login failed", error);
      }
    },
    setToken(responseData) {
      this.$store.dispatch("login", responseData);
      this.$i18n.locale = responseData.user.preferred_language;
    },
    checkForm(e) {
      if (this.formData.username != "" && this.formData.password != "") return true;
      let form = document.querySelector(".needs-validation");
      form.classList.add("was-validated");
      e.preventDefault();
      return false;
    },
    goBack() {
      this.$router.push("/");
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

.success {
  color: green;
}
</style>
