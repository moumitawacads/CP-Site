<template>
  <div class="container">
    <div class="row d-flex justify-content-center">
      <div class="col-md-6" v-if="!calendarMessageShow">
        <div class="card px-4 py-4">
          <h2 class="text-3xl font-bold text-center mb-6">{{ $t("verify_code") }}</h2>
          <form class="needs-validation" novalidate ref="verificationForm" @submit.prevent="verify">
            <input v-model="formData.verification_code" type="text" class="form-control" :placeholder="$t('enter_code')"
              required />
            <div class="invalid-feedback">{{ $t(errors.code) }}</div>
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
          <span class="d-flex err mt-2" v-if="errorMsg">{{ $t(errorMsg) }}</span>
        </div>
      </div>
      <div class="col-md-6" v-if="calendarMessageShow">
        <p>
          You haven’t picked a time for the meeting yet. To make things easier, you can
          use the link below to choose a time that works best for you.
        </p>
        <a class="d-flex justify-content-center" href="/calendar">Choose Time</a>
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
        user_id: "",
        verification_code: "",
      },
      errorMsg: null,
      disabledBtn: false,
      calendarMessageShow: false,
      errors: {
        code: "Code field is required.",
      },
    };
  },
  mounted() {
    let user = this.$ls.get("user");
    this.formData.user_id = user.id;
  },
  methods: {
    async verify() {
      this.disabledBtn = true;
      let validation = this.checkForm(event);
      if (validation) {
        try {
          this.errorMsg = null;
          const response = await axios.post("/api/verify-code", this.formData);
          if (response && response.status == 200) {
            this.disabledBtn = false;
            // if (
            //   response.data.user.scheduled_meeting_start_datetime &&
            //   response.data.user.scheduled_meeting_end_datetime
            // ) {
            this.$store.dispatch("login", response.data);
            this.$i18n.locale = response.data.user.preferred_language;
            this.$router.push({ name: "dashboard" });
            // } else {
            //   this.$ls.set("responseData", response.data);
            //   this.calendarMessageShow = true;
            //   // this.$router.push({ name: "calendar" });
            // }
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
      if (this.formData.verification_code != "") return true;
      let form = document.querySelector(".needs-validation");
      form.classList.add("was-validated");
      e.preventDefault();
      return false;
    },
    goBack() {
      this.$router.push("/login");
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
