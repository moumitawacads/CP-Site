<template>
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-2 bg-dark p-0">
        <nav class="navbar flex-column bg-dark text-white p-0">
          <ul class="nav flex-column w-100 " style="min-height: 100vh">
            <li class="nav-item">
              <router-link to="/admin" class="nav-link text-white" :class="{ active: isDashboard }">
                Dashboard
              </router-link>
            </li>
            <li class="nav-item">
              <div class="nav justify-content-between" @click="toggleServices">
                <a class="nav-link text-white" href="#">Services
                </a>
                <span class="p-1 pr-1" :class="{ 'arrow-up': isServicesOpen, 'arrow-down': !isServicesOpen }"></span>
              </div>
              <!-- Submenu -->
              <ul v-if="isServicesOpen" class="nav flex-column bg-secondary">
                <li class="nav-item">
                  <router-link to="/admin/services/assessments" class="nav-link text-white submenu"
                    :class="{ active: isAssessmentsServices }">
                    Assessments
                  </router-link>
                </li>
                <li class="nav-item">
                  <router-link to="/admin/services/literacy-diagnostics" class="nav-link text-white submenu"
                    :class="{ active: isLiteracyDiagnosticsServices }">
                    Literacy Diagnostics
                  </router-link>
                </li>
              </ul>
            </li>
            <li class="nav-item">
              <router-link to="/admin/homework-helpers" class="nav-link text-white"
                :class="{ active: isHomeworkHelpersServices }">
                Homework Helpers
              </router-link>
            </li>
            <li class="nav-item">
              <router-link to="/admin/clinicians" class="nav-link text-white" :class="{ active: isCliniciansServices }">
                Clinicians
              </router-link>
            </li>
            <li class="nav-item">
              <router-link to="/admin/parents" class="nav-link text-white" :class="{ active: isParentsServices }">
                Parents
              </router-link>
            </li>
            <li class="nav-item">
              <router-link to="/admin/whats-new" class="nav-link text-white" :class="{ active: isWhatsNew }">
                What's New
              </router-link>
            </li>
            <li class="nav-item">
              <router-link to="/admin/category" class="nav-link text-white"
                :class="{ active: isCategory }">Category</router-link>
            </li>
          </ul>
        </nav>
      </div>
      <div class="col-md-10 py-4 px-4">
        <section v-if="$route.path === '/admin'">
          <Cards @toggleServices="toggleServices" />
        </section>
        <router-view></router-view>
      </div>
    </div>
  </div>
</template>

<script>
import Cards from './Cards.vue';

export default {
  name: "AdminDashboard",
  components: {
    Cards
  },
  data() {
    return {
      isServicesOpen: false, // Tracks if the Services submenu is open
    }
  },
  computed: {
    isDashboard() {
      return (
        this.$route.name === "admin-dashboard"
      );
    },
    isWhatsNew() {
      return (
        this.$route.name === "whats-new" ||
        this.$route.name === "add-whats-new" ||
        this.$route.name === "edit-whats-new"
      );
    },
    isCategory() {
      return (
        this.$route.name === "category" ||
        this.$route.name === "add-category" ||
        this.$route.name === "edit-category"
      );
    },
    isAssessmentsServices() {
      return (
        this.$route.name === "assessments" ||
        this.$route.name === "assign-assessment"
      );
    },
    isLiteracyDiagnosticsServices() {
      return (
        this.$route.name === "literacy-diagnostics" ||
        this.$route.name === "assign-literacy-diagnostic"
      );
    },
    isHomeworkHelpersServices() {
      return (
        this.$route.name === "homework-helpers" ||
        this.$route.name === "assign-homework-helper"
      );
    },
    isCliniciansServices() {
      return (
        this.$route.name === "clinicians" ||
        this.$route.name === "view-clinician-learners"
      );
    },
    isParentsServices() {
      return (
        this.$route.name === "parents" ||
        this.$route.name === "view-parents-learners"
      );
    },
  },
  mounted() {
    if (this.isAssessmentsServices || this.isLiteracyDiagnosticsServices) {
      this.isServicesOpen = true;
    }
  },
  methods: {
    toggleServices() {
      if (!this.isAssessmentsServices && !this.isLiteracyDiagnosticsServices) {
        this.isServicesOpen = !this.isServicesOpen;
      }
    },
    logout(e) {
      e.preventDefault();
      this.$store.dispatch("logout", "admin");
    },
  },
  watch: {
    isAssessmentsServices(value) {
      if (!value) {
        this.toggleServices();
      }
    },
    isLiteracyDiagnosticsServices(value) {
      if (!value) {
        this.toggleServices();
      }
    },
  }
};
</script>

<style scoped>
.navbar-title {
  white-space: pre-wrap;
  color: #333;
  margin: 0;
  background: springgreen;
  padding: 0.5rem;
  text-align: center;
  font-weight: 700;
  font-size: 22px;
  line-height: 24px;
}

.p-5rem {
  padding: 0.5rem;
}

.nav-link.active {
  background-color: #0a58ca;
  color: white !important;
}

.navbar {
  position: fixed;
  width: 16.67%;
}

.submenu {
  padding-left: 2rem;
}

.arrow-down::after {
  content: "▼";
  font-size: 1.19rem;
}

.arrow-up::after {
  content: "▲";
  font-size: 1.19rem;
}

.nav-link {
  cursor: pointer;
}

.pr-1 {
  padding-right: 13px !important;
}
</style>
