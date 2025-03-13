<template>
  <div>
    <header class="d-flex flex-wrap align-items-center justify-content-center py-3 border-bottom sticky" :class="[
      !isTokenValid ? 'justify-content-md-between' : '',
      //isSticky ? 'sticky' : '',
    ]" style="transition: top 0.3s ease-in-out, box-shadow 0.3s ease-in-out">
      <div class="col-md-2 nav justify-content-center">
        <router-link v-if="isAdminRoute && isTokenValid" class="navbar-brand navbar-title" to="/admin">Admin
          Dashboard</router-link>
      </div>

      <template v-if="!isAdminRoute">
        <ul class="nav col-8 mb-2 justify-content-center mb-md-0" v-if="!isTokenValid">
          <li>
            <router-link to="/" class="nav-link px-2" active-class="link-dark">Home</router-link>
          </li>
          <li>
            <router-link to="/price" class="nav-link px-2" active-class="link-dark">Pricing</router-link>
          </li>
          <li>
            <router-link to="/faq" class="nav-link px-2" active-class="link-dark">FAQs</router-link>
          </li>
        </ul>
        <div class="col-8 text-end" v-if="isTokenValid && userData && userData.role_slug != 'administrator'">
          <p class="login-time">
            {{ $t("last_login") }}:
            <span>{{ formatDate(userData.last_login_datetime) }}</span>
          </p>
          <p class="logout-time">
            {{ $t("last_logout") }}:
            <span>{{ formatDate(userData.last_logout_datetime) }}</span>
          </p>
        </div>
      </template>
      <template v-else>
        <ul class="nav col-8 mb-2 justify-content-center mb-md-0"></ul>
      </template>

      <div class="col-md-1 justify-content-end nav">
        <router-link to="/login" class="btn btn-outline-primary"
          v-if="!isTokenValid && !isAdminRoute">Login</router-link>
        <div v-if="isTokenValid && userData">
          <div class="dropdown text-end">
            <a href="#" class="d-block link-dark text-decoration-none dropdown-toggle" id="dropdownUser1"
              data-bs-toggle="dropdown" aria-expanded="false">
              <img src="../../img/no-user.png" alt="mdo" width="32" height="32" class="rounded-circle" />
            </a>
            <ul class="dropdown-menu text-small" aria-labelledby="dropdownUser1" style="">
              <li>
                <a class="dropdown-item btn btn-danger" @click="isAdminRoute ? logout('admin') : logout()">
                  {{ $t("logout") }}
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <div class="col-md-1 justify-content-center nav">
        <LanguageSelectBox :isTokenValid="isTokenValid" />
      </div>
    </header>
  </div>
</template>

<script>
import moment from "moment";
import LanguageSelectBox from "../components/LanguageSelectBox.vue";

export default {
  name: "Header",
  props: ["isAdminRoute"],
  components: {
    LanguageSelectBox,
  },
  data() {
    return {
      userData: null,
      isSticky: false,
    };
  },
  computed: {
    isTokenValid() {
      const tokenExpiry = this.$ls.get("token_expiry");
      return this.$store.state.token && tokenExpiry && Date.now() <= tokenExpiry;
    },
  },
  mounted() {
    this.userData = this.$store.state.user;
  },
  methods: {
    async logout(value = null) {
      this.$store.dispatch("logout", value);
      this.$i18n.locale = "en";
    },
    formatDate(value) {
      if (value) {
        let stillUtc = moment.utc(value).toDate();
        return moment(stillUtc).local().format("MMM DD, YYYY - hh:mm A");
      }
    },
  },
  watch: {
    "$store.state.user": {
      handler(newValue) {
        this.userData = newValue;
      },
      deep: true,
    },
  },
};
</script>

<style scoped>
.login-time {
  margin: 0;
  font-size: 14px;
  text-align: right;
}

.logout-time {
  margin: 0;
  font-size: 14px;
  text-align: right;
}

.session-time {
  margin: 0;
  font-size: 14px;
  text-align: right;
}

.sticky {
  position: fixed;
  top: 0;
  width: 100%;
  z-index: 1000;
  background-color: white;
  box-shadow: 0 2px 4px -1px rgba(0, 0, 0, 0.2), 0 4px 5px 0 rgba(0, 0, 0, 0.14),
    0 1px 10px 0 rgba(0, 0, 0, 0.12);
  /*0 2px 5px rgba(0, 0, 0, 0.1);*/
  transition: top 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
}
</style>
