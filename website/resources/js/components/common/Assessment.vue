<template>
  <div class="mt-4" id="accordionPromotion">
    <table class="table table-striped table-bordered table-responsive" v-if="!learnerDeclarationFormsShow">
      <thead>
        <tr>
          <th>Name</th>
          <th>Age</th>
          <th>{{ responseData && responseData.user.role_slug === 'parents' ? 'Clinician' : 'Parent' }} Name</th>
          <th>Schedule Meeting Start Time</th>
          <th>Schedule Meeting End Time</th>
          <th>Session Status</th>
          <th>Additional Comments</th>
        </tr>
      </thead>
      <tbody>
        <template v-if="responseData && responseData.user.role_slug === 'parents'">
          <template v-if="!haveAssessmentService">
            <tr class="blur-preview">
              <td class="blurred-content">John Doe</td>
              <td class="blurred-content">12</td>
              <td class="blurred-content">Adam Page</td>
              <td class="blurred-content">2025-01-22 10:00 AM</td>
              <td class="blurred-content">2025-01-22 10:30 AM</td>
              <td class="blurred-content">Not Finish</td>
              <!-- Overlay -->
              <td colspan="7" class="overlay">
                Add Assessment service to view full details</td>
            </tr>
          </template>
          <template v-else-if="userAssessments && userAssessments.length > 0">
            <tr v-for="(userAssessment, index) in userAssessments" :key="index + 1">
              <td>
                <router-link :to="'/learner-dashboard/' + userAssessment.learner_data.id">{{
                  userAssessment.learner_data.user.fullname }}</router-link>
              </td>
              <td>{{ userAssessment.learner_data.learner_age }}</td>
              <td>{{ getClinicianName(userAssessment.learner_data.clinicians_data) }}</td>
              <td>
                {{
                  userAssessment.user_assessment_data.schecdule_meeting_start_time
                }}
              </td>
              <td>
                {{
                  userAssessment.user_assessment_data.schecdule_meeting_start_time
                }}
              </td>
              <td>
                <span class="badge"
                  :class="userAssessment.user_assessment_data.status === 0 ? 'bg-danger' : 'bg-success'">{{
                    userAssessment.user_assessment_data.status === 0
                      ? "Not finish"
                      : "Finished"
                  }}</span>
              </td>
              <td>{{
                userAssessment.learner_data.user.additional_comments
                }}</td>
            </tr>
          </template>
          <template v-else>
            <tr>
              <td class="text-center" colspan="7">No record found!</td>
            </tr>
          </template>
        </template>
        <template v-else>
          <template v-if="userAssessments && userAssessments.length > 0">
            <tr v-for="(userAssessment, index) in userAssessments" :key="index + 1">
              <td :class="isNewLearner(userAssessment.learner_data) ? 'new-learner' : ''">
                <router-link :to="'/learner-dashboard/' + userAssessment.learner_data.id"
                  @click="isNewLearner(userAssessment.learner_data) ? closeNewBadge(userAssessment.learner_data.id) : null">{{
                    getFullName(userAssessment.learner_data.user.fullname) }}</router-link><span class="badge bg-danger"
                  v-if="isNewLearner(userAssessment.learner_data)">new</span>
              </td>
              <td :class="isNewLearner(userAssessment.learner_data) ? 'new-learner' : ''">{{
                userAssessment.learner_data.learner_age }}</td>
              <td :class="isNewLearner(userAssessment.learner_data) ? 'new-learner' : ''">{{
                userAssessment.learner_data.parent_data.user.fullname }}</td>
              <td :class="isNewLearner(userAssessment.learner_data) ? 'new-learner' : ''">
                {{
                  userAssessment.user_assessment_data.schecdule_meeting_start_time
                }}
              </td>
              <td :class="isNewLearner(userAssessment.learner_data) ? 'new-learner' : ''">
                {{
                  userAssessment.user_assessment_data.schecdule_meeting_start_time
                }}
              </td>
              <td :class="isNewLearner(userAssessment.learner_data) ? 'new-learner' : ''">
                <span class="badge"
                  :class="userAssessment.user_assessment_data.status === 0 ? 'bg-danger' : 'bg-success'">{{
                    userAssessment.user_assessment_data.status === 0
                      ? "Not finish"
                      : "Finished"
                  }}</span>
              </td>
              <td :class="isNewLearner(userAssessment.learner_data) ? 'new-learner' : ''">{{
                userAssessment.learner_data.user.additional_comments
                }}</td>
            </tr>
          </template>
          <template v-else>
            <tr>
              <td class="text-center" colspan="7">No record found!</td>
            </tr>
          </template>
        </template>
      </tbody>
    </table>
  </div>
</template>

<script>
import moment from "moment-timezone";
import axios from "../../axios";

export default {
  name: "Assessment",
  props: ["userData"],
  emits: ["removeNewLearnerId", "navigate"],
  data() {
    return {
      responseData: null,
      mappedSubscriptions: [],
      userAssessments: null,
      pagination: {
        current_page: 1,
        last_page: 1,
        links: [],
        data: [],
      },
      learnerDeclarationFormsShow: false,
      learnerDeclarationForms: null
    };
  },
  computed: {
    haveAssessmentService() {
      return (
        this.userData.user.plans.filter((plan) => {
          return plan.plan.name === "Mini Artic Assessment";
        }).length > 0
      );
    },
  },
  mounted() {
    this.responseData = this.userData;
    this.getLearnerDetails();
  },
  methods: {
    async fetchMappedSubscriptions() {
      const subscriptions = this.responseData.user.subscriptions;

      const promises = subscriptions.map(async (subscription) => {
        const planDetails = await this.getPlanDetails(subscription.stripe_price);
        return {
          ...subscription,
          plan_details: planDetails,
        };
      });

      this.mappedSubscriptions = await Promise.all(promises);
    },
    async getPlanDetails(stripe_price_id) {
      let planDetails;
      try {
        const response = await axios.get(
          "/api/plan-price/?stripe_price_id=" + stripe_price_id,
          {
            headers: {
              Authorization: "Bearer " + this.$store.state.token,
            },
          }
        );
        if (response && response.status == 200) {
          planDetails = response.data[0];
          // this.responseData = response.data.data[0];
        }
      } catch (error) {
        console.error("Error uploading file:", error);
      }

      return planDetails;
    },
    async closeNewBadge(learnerId) {
      try {
        const response = await axios.put(
          "/api/user/" + learnerId + "/update-viewed-assessment",
          {
            assessment_viewed: 0,
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
    scheduleTimeFormat(value, selectedTimezone) {
      // let userTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone; // Get user's timezone
      if (value) {
        return moment
          .tz(value, selectedTimezone)
          .clone() // clone to avoid mutation
          .local() // convert to local timezone
          .format("hh:mm A - ddd, MMM DD, YYYY");
      }
    },
    formatDate(value) {
      if (value) {
        return moment(String(value)).format("DD/MM/YYYY hh:mm:ss");
      }
    },
    async getLearnerDetails(page = 1) {
      try {
        const response = await axios.get(this.getUrl(page), {
          headers: {
            Authorization: "Bearer " + this.$store.state.token,
          },
        });
        if (response && response.status == 200) {
          this.userAssessments = response.data.data;
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
        "/assessment/?page=" +
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
          return this.userData.user.role_slug === "clinician" && clinicianData.clinician_learner_link_data.assessment_viewed === 1;
        }
      }
    },
    getClinicianName(clinicians_data) {
      let clinician = clinicians_data.filter((clinician, index) => {
        return clinician.clinician_learner_link_data.service_type === 'mini-artic-assessment'
      });
      return clinician.length > 0 ? clinician[0].clinician_data.user.fullname : "";
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
h1 {
  color: #4caf50;
}

.err {
  color: red;
}

.p-12 {
  padding: 12px;
}

.new-assessment {
  background-color: lightgreen;
  cursor: pointer;
}

.new-background-transparent {
  background-color: transparent;
}

.new-table-border {
  border: 1px slategray;
}

.text-right {
  text-align: right;
}

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

.link-primary {
  cursor: pointer;
}
</style>
