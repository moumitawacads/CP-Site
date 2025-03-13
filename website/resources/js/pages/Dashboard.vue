<template>
  <div class="mx-md-5">
    <div class="row p1" v-if="userData">
      <div class="alert alert-secondary" role="alert">
        <div class="d-flex justify-content-between">
          <h4 class="alert-heading">Hey {{ getFullName(userData.fullname) }}, welcome back to {{ userData.role_slug }}
            dashboard!</h4>
          <!-- <button type="button" class="btn btn-close btn-sm" data-dismiss="alert" aria-label="Close"></button> -->
        </div>
        <p>
          Manage your learners effortlessly—add new learners, track their game progress, review assessments, assign
          homework, and stay updated with the latest in What's New. Let’s make learning fun and effective!
        </p>
      </div>
    </div>
    <!-- <div class="row">
      <div class="col-md-8">
        <h4 v-if="userData">Hey {{ getFullName(userData.fullname) }},</h4>
      </div>
    </div> -->
    <template v-if="userData && userData.role_slug === 'clinician'">
      <ClinicianDashboard />
    </template>
    <template v-else-if="userData && userData.role_slug === 'learner'">
      <LearnerDashboard />
    </template>
    <template v-else-if="userData && userData.role_slug === 'parents'">
      <ParentsDashboard />
    </template>
    <template v-else-if="userData && userData.role_slug === 'administrator'">
      <AdminDashboard />
    </template>
  </div>
</template>

<script>
import moment from "moment";
import ClinicianDashboard from "../components/clinicians/ClinicianDashboard.vue";
import LearnerDashboard from "../components/learners/LearnerDashboard.vue";
import ParentsDashboard from "../components/parents/ParentsDashboard.vue";
import AdminDashboard from "../components/admin/AdminDashboard.vue";

export default {
  name: "Dashboard",
  components: {
    ClinicianDashboard,
    LearnerDashboard,
    ParentsDashboard,
    AdminDashboard,
  },
  data() {
    return {
      userData: null,
    };
  },
  mounted() {
    this.userData = this.$store.state.user;
  },
  methods: {
    getFullName(name) {
      let words = name.split(" ");
      if (words.length > 1) {
        return words[0] + " " + words[1].substring(0, 1);
      } else {
        return words[0];
      }
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

.fa-bell {
  color: rgb(30, 48, 80);
  font-size: 24px;
}

.iconClass {
  position: relative;
}

.iconClass span {
  position: absolute;
  top: 0px;
  right: 0px;
  display: block;
  background: orange;
}

.p1 {
  padding: 0 1rem;
}
</style>
