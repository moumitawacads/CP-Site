<template>
  <section class="mt-4">
    <table class="table table-striped table-bordered table-responsive"
      @click="!isProfessionalPlusPlan ? addService($event) : null">
      <thead>
        <tr>
          <th>Name</th>
          <th>Age</th>
          <th>
            {{ userData.user.role_slug === 'parents' ? $t("clinician_name") : "Parent Name" }}
          </th>
          <th>Report</th>
          <th>{{ $t("target_sound") }}</th>
          <th>{{ $t("last_game_played") }}</th>
          <th>{{ $t("history_of_played_games") }}</th>
          <th v-if="userData.user.role_slug === 'clinician'">
            {{ $t("homework_helper") }}
          </th>
        </tr>
      </thead>
      <tbody>
        <template v-if="!isProfessionalPlusPlan">
          <tr class="blur-preview">
            <td class="blurred-content">John Doe</td>
            <td class="blurred-content">12</td>
            <td class="blurred-content">
              Dr. Smith
            </td>
            <td class="blurred-content">Report Summary</td>
            <td class="blurred-content">Target Sound Example</td>
            <td class="blurred-content">Memory Game</td>
            <td class="blurred-content">
              <ul>
                <li>Game 1</li>
                <li>Game 2</li>
              </ul>
            </td>
            <td v-if="userData.user.role_slug === 'clinician'" class="blurred-content">
              Homework Example
            </td>
            <!-- Overlay -->
            <td colspan="9" class="overlay">
              Upgrade to "Professional Plus" to view full details
            </td>
          </tr>
        </template>
        <template v-else-if="userHomeworkHelpers && userHomeworkHelpers.length > 0">
          <tr v-for="(userHomeworkHelper, index) in userHomeworkHelpers" :key="index + 1">
            <td :class="isNewLearner(userHomeworkHelper.learner_data) ? 'new-learner' : ''">
              <router-link :to="'/learner-dashboard/' + userHomeworkHelper.learner_data.id"
                @click="isNewLearner(userHomeworkHelper.learner_data) ? closeNewBadge(userHomeworkHelper.learner_data.id) : null">{{
                  getFullName(userHomeworkHelper.learner_data.user.fullname) }}</router-link><span class="badge bg-danger"
                v-if="isNewLearner(userHomeworkHelper.learner_data)">new</span>
            </td>
            <td :class="isNewLearner(userHomeworkHelper.learner_data) ? 'new-learner' : ''">
              {{
                userHomeworkHelper.learner_data.learner_age }}</td>
            <td :class="isNewLearner(userHomeworkHelper.learner_data) ? 'new-learner' : ''">
              {{ userData.user.role_slug === 'parents' ?
                getClinicianName(userHomeworkHelper.learner_data.clinicians_data) :
                userHomeworkHelper.learner_data.parent_data ? userHomeworkHelper.learner_data.parent_data.user.fullname :
                  "" }}
            </td>
            <td :class="isNewLearner(userHomeworkHelper.learner_data) ? 'new-learner' : ''">
            </td>
            <td :class="isNewLearner(userHomeworkHelper.learner_data) ? 'new-learner' : ''">
              <template v-if="userHomeworkHelper.learner_data.sessions.length > 0">
                <div
                  v-for="(gameSession, gameIndex) in sortedSessions(userHomeworkHelper.learner_data.sessions).slice(-2)"
                  :key="gameSession.id">
                  <label>
                    <b>Session {{ userHomeworkHelper.learner_data.sessions.length - gameIndex }}: </b>
                  </label>
                  <p>{{ gameSession.target_sound }}</p>
                </div>
                <router-link :to="'/learner-dashboard/' + userHomeworkHelper.learner_data.id"
                  class="link-primary custom-link"
                  v-if="sortedSessions(userHomeworkHelper.learner_data.sessions).length > 2">Show
                  More</router-link>
              </template>
            </td>
            <td :class="isNewLearner(userHomeworkHelper.learner_data) ? 'new-learner' : ''">
              <div v-if="userHomeworkHelper.learner_data.sessions.length > 0">
                {{
                  userHomeworkHelper.learner_data.sessions[
                    userHomeworkHelper.learner_data.sessions.length - 1
                  ].session_games[
                    userHomeworkHelper.learner_data.sessions[
                      userHomeworkHelper.learner_data.sessions.length - 1
                    ].session_games.length - 1
                  ].game_name
                }}
              </div>
            </td>
            <td :class="isNewLearner(userHomeworkHelper.learner_data) ? 'new-learner' : ''">
              <template v-if="userHomeworkHelper.learner_data.sessions.length > 0">
                <div
                  v-for="(gameSession, gameSessionIndex) in sortedSessions(userHomeworkHelper.learner_data.sessions).slice(-2)"
                  :key="gameSession.id">
                  <label>
                    <b>Session {{ userHomeworkHelper.learner_data.sessions.length - gameSessionIndex }}: </b>
                  </label>
                  <ul>
                    <li v-for="game in gameSession.session_games" :key="game.id">
                      {{ game.game_name }}
                    </li>
                  </ul>
                </div>
                <router-link :to="'/learner-dashboard/' + userHomeworkHelper.learner_data.id"
                  class="link-primary custom-link"
                  v-if="sortedSessions(userHomeworkHelper.learner_data.sessions).length > 2">Show
                  More</router-link>
              </template>
            </td>
            <td :class="isNewLearner(userHomeworkHelper.learner_data) ? 'new-learner' : ''"
              v-if="userData.user.role_slug === 'clinician'"></td>
          </tr>
        </template>
        <template v-else-if="userHomeworkHelpers && userHomeworkHelpers.length === 0">
          <tr>
            <td colspan="8" class="text-center">No records available</td>
          </tr>
        </template>
      </tbody>
    </table>
  </section>
</template>

<script>
import axios from "../../axios";
export default {
  props: ["userData"],
  data() {
    return {
      userHomeworkHelpers: null,
      pagination: {
        current_page: 1,
        last_page: 1,
        links: [],
        data: [],
      },
    };
  },
  computed: {
    isProfessionalPlusPlan() {
      return (
        this.userData.user.plans.filter((plan) => {
          return plan.plan.name === "Professional Plus";
        }).length > 0
      );
    },
  },
  mounted() {
    this.getLearnerDetails();
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
          this.userHomeworkHelpers = response.data.data;
          this.pagination = response.data.pagination;
          // console.log(this.learnersData);
        }
      } catch (error) {
        console.error("Error uploading file:", error);
      }
    },
    getUrl(page) {
      return (
        "/api/" +
        this.userData.user.role_slug +
        "/" +
        this.userData.id +
        "/homework-helper/?page=" +
        page
      );
    },
    addService(e) {
      e.preventDefault();
      this.$router.push({
        path: "/price",
        query:
          this.userData.user.role_slug === "parents"
            ? { parent_id: this.userData.id, plan: "upgrade" }
            : { clinician_id: this.userData.id, plan: "upgrade" },
      });
    },
    getClinicianName(clinicians_data) {
      let clinician = clinicians_data.filter((clinician, index) => {
        return clinician.clinician_learner_link_data.service_type === 'homework-helper'
      });
      return clinician.length > 0 ? clinician[0].clinician_data.user.fullname : "";
    },
    sortedSessions(learnerDataSessions) {
      return learnerDataSessions.sort((a, b) => {
        return new Date(b.session_start_time) - new Date(a.session_start_time);
      });
    },
    isNewLearner(learnerData) {
      if (this.$store.state.providerData) {
        let clinician = learnerData.clinicians_data.filter(clinician => {
          return clinician.clinician_learner_link_data.clinician_id == this.$store.state.providerData.id;
        });
        if (clinician.length > 0) {
          let clinicianData = clinician[0];
          return this.userData.user.role_slug === "clinician" && clinicianData.clinician_learner_link_data.homework_helper_viewed === 1;
        }
      }
    },
    async closeNewBadge(learnerId) {
      try {
        const response = await axios.put(
          "/api/user/" + learnerId + "/update-viewed-homework-helper",
          {
            homework_helper_viewed: 0,
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
