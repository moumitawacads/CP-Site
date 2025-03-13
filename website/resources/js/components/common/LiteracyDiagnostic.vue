<template>
  <section class="mt-4">
    <table class="table table-striped table-bordered table-responsive">
      <thead>
        <tr>
          <th>Name</th>
          <th>Age</th>
          <th>{{ responseData && responseData.user.role_slug === 'parents' ? 'Clinician' : 'Parent' }} Name</th>
          <th>Schedule Meeting Start Time</th>
          <th>Schedule Meeting End Time</th>
          <th>Session Status</th>
        </tr>
      </thead>
      <tbody>
        <template v-if="responseData && responseData.user.role_slug === 'parents'">
          <template v-if="userLiteracyDiagnostics && userLiteracyDiagnostics.length > 0">
            <tr v-for="(userLiteracyDiagnostic, index) in userLiteracyDiagnostics" :key="index + 1">
              <td><router-link :to="'/learner-dashboard/' + userLiteracyDiagnostic.learner_data.id">{{
                userLiteracyDiagnostic.learner_data.user.fullname }}</router-link></td>
              <td>{{ userLiteracyDiagnostic.learner_data.learner_age }}</td>
              <td>{{ getClinicianName(userLiteracyDiagnostic.learner_data.clinicians_data) }}</td>
              <td>
                {{
                  userLiteracyDiagnostic.user_literacy_diagnostic_data
                    .schecdule_meeting_start_time
                }}
              </td>
              <td>
                {{
                  userLiteracyDiagnostic.user_literacy_diagnostic_data
                    .schecdule_meeting_start_time
                }}
              </td>
              <td>
                <span class="badge"
                  :class="userLiteracyDiagnostic.user_literacy_diagnostic_data.status === 0 ? 'bg-danger' : 'bg-success'">{{
                    userLiteracyDiagnostic.user_literacy_diagnostic_data.status === 0
                      ? "Not finish"
                      : "Finished"
                  }}</span>
              </td>
            </tr>
          </template>
          <template v-else>
            <tr class="blur-preview">
              <td class="blurred-content">John Doe</td>
              <td class="blurred-content">12</td>
              <td class="blurred-content">2025-01-22 10:00 AM</td>
              <td class="blurred-content">2025-01-22 10:30 AM</td>
              <td class="blurred-content">Not Finish</td>
              <!-- Overlay -->
              <td colspan="8" class="overlay">Add Literacy service to view full details</td>
            </tr>
          </template>
        </template>
        <template v-else-if="responseData && responseData.user.role_slug === 'clinician'">
          <template v-if="userLiteracyDiagnostics && userLiteracyDiagnostics.length > 0">
            <tr v-for="(userLiteracyDiagnostic, index) in userLiteracyDiagnostics" :key="index + 1">
              <td :class="isNewLearner(userLiteracyDiagnostic.learner_data) ? 'new-learner' : ''">
                <router-link :to="'/learner-dashboard/' + userLiteracyDiagnostic.learner_data.id"
                  @click="isNewLearner(userLiteracyDiagnostic.learner_data) ? closeNewBadge(userLiteracyDiagnostic.learner_data.id) : null">{{
                    getFullName(userLiteracyDiagnostic.learner_data.user.fullname) }}</router-link><span
                  class="badge bg-danger" v-if="isNewLearner(userLiteracyDiagnostic.learner_data)">new</span>
              </td>
              <td :class="isNewLearner(userLiteracyDiagnostic.learner_data) ? 'new-learner' : ''">
                {{
                  userLiteracyDiagnostic.learner_data.learner_age }}</td>
              <td :class="isNewLearner(userLiteracyDiagnostic.learner_data) ? 'new-learner' : ''">
                {{
                  getClinicianName(userLiteracyDiagnostic.learner_data.clinicians_data) }}</td>
              <td :class="isNewLearner(userLiteracyDiagnostic.learner_data) ? 'new-learner' : ''">
                {{
                  userLiteracyDiagnostic.user_literacy_diagnostic_data
                    .schecdule_meeting_start_time
                }}
              </td>
              <td :class="isNewLearner(userLiteracyDiagnostic.learner_data) ? 'new-learner' : ''">
                {{
                  userLiteracyDiagnostic.user_literacy_diagnostic_data
                    .schecdule_meeting_start_time
                }}
              </td>
              <td :class="isNewLearner(userLiteracyDiagnostic.learner_data) ? 'new-learner' : ''">
                <span class="badge"
                  :class="userLiteracyDiagnostic.user_literacy_diagnostic_data.status === 0 ? 'bg-danger' : 'bg-success'">{{
                    userLiteracyDiagnostic.user_literacy_diagnostic_data.status === 0
                      ? "Not finish"
                      : "Finished"
                  }}</span>
              </td>
            </tr>
          </template>
          <template v-else>
            <tr>
              <td class="text-center" colspan="6">No record found!</td>
            </tr>
          </template>
        </template>
      </tbody>
    </table>
  </section>
</template>

<script>
import moment from "moment-timezone";
import axios from "../../axios";
export default {
  name: "LiteracyDiagnostic",
  props: ["userData"],
  data() {
    return {
      responseData: null,
      userLiteracyDiagnostics: null,
      pagination: {
        current_page: 1,
        last_page: 1,
        links: [],
        data: [],
      },
    };
  },
  mounted() {
    this.responseData = this.userData;

    this.getLearnerDetails();
    // console.log(this.responseData);
  },
  methods: {
    async getLearnerDetails(page = 1) {
      try {
        const response = await axios.get(this.getUrl(page), {
          headers: {
            Authorization: "Bearer " + this.$store.state.token,
          },
        });
        if (response && response.status == 200) {
          this.userLiteracyDiagnostics = response.data.data;
          this.pagination = response.data.pagination;
          // console.log(this.learnersData);
        }
      } catch (error) {
        console.error("Error uploading file:", error);
      }
    },
    getUrl(page) {
      let userData = this.$store.state.providerData;
      return (
        "/api/" +
        userData.user.role_slug +
        "/" +
        userData.id +
        "/literacy-diagnostic/?page=" +
        page
      );
    },
    isNewLearner(learnerData) {
      if (this.$store.state.providerData) {
        let clinician = learnerData.clinicians_data.filter(clinician => {
          return clinician.clinician_learner_link_data.clinician_id == this.$store.state.providerData.id;
        });
        if (clinician.length > 0) {
          let clinicianData = clinician[0];
          return this.userData.user.role_slug === "clinician" && clinicianData.clinician_learner_link_data.literacy_viewed === 1;
        }
      }
    },
    getClinicianName(clinicians_data) {
      let clinician = clinicians_data.filter((clinician, index) => {
        return clinician.clinician_learner_link_data.service_type === 'mini-literacy-diagnostic'
      });
      return clinician.length > 0 ? clinician[0].clinician_data.user.fullname : "";
    },
    async closeNewBadge(learnerId) {
      try {
        const response = await axios.put(
          "/api/user/" + learnerId + "/update-viewed-literacy",
          {
            literacy_viewed: 0,
            clinician_id: this.$store.state.providerData.id
          },
          {
            headers: {
              Authorization: "Bearer " + this.$store.state.token,
            },
          }
        );
        if (response && response.status == 200) {
          this.getLearnerDetails();
        }
      } catch (error) {
        console.error("Error uploading file:", error);
      }
    },
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
.blur-preview {
  position: relative;
}

.blur-preview .blurred-content {
  filter: blur(3px);
  /* Applies blur effect to the content */
  pointer-events: none;
  /* Prevents interaction with blurred content */
}

.blur-preview .overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(255, 255, 255, 0.8);
  /* Semi-transparent overlay */
  display: flex;
  justify-content: center;
  align-items: center;
  font-weight: bold;
  color: #333;
  z-index: 1;
  /* Ensures the overlay is on top of the blurred content */
  pointer-events: none;
  /* Allows clicks to pass through to table */
}

.new-learner {
  background-color: lightgreen;
}
</style>
