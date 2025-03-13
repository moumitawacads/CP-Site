<template>
  <div class="container" v-if="learnerDetails">
    <div class="row gy-4 gy-lg-0">
      <div class="col-12 col-lg-4 col-xl-3">
        <div class="row gy-4">
          <div class="col-12">
            <div class="card widget-card shadow-sm">
              <div class="card-header text-bg-primary">
                {{ $t("Welcome") }},
                {{ getFullName(learnerDetails.learner_data.user.fullname) }}
              </div>
              <div class="card-body">
                <div class="text-center mb-3">
                  <img src="../../../img/no-user.png" class="img-fluid rounded-circle" alt="Luna John" />
                </div>
                <div class="text-center">
                  <router-link to="/dashboard" class="btn btn-outline-primary">{{
                    $t("back")
                  }}</router-link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-12 col-lg-8 col-xl-9">
        <div class="card widget-card shadow-sm">
          <div class="card-body p-4">
            <ul class="nav nav-tabs" id="profileTab" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-tab-pane"
                  type="button" role="tab" aria-controls="profile-tab-pane" aria-selected="true">
                  {{ $t("Profile") }}
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="games-sessions-tab" data-bs-toggle="tab"
                  data-bs-target="#games-sessions-tab-pane" type="button" role="tab"
                  aria-controls="games-sessions-tab-pane" aria-selected="false">
                  Games Played
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="services-tab" data-bs-toggle="tab" data-bs-target="#services-tab-pane"
                  type="button" role="tab" aria-controls="services-tab-pane" aria-selected="false">
                  Services
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="declaration-tab" data-bs-toggle="tab"
                  data-bs-target="#declaration-tab-pane" type="button" role="tab" aria-controls="declaration-tab-pane"
                  aria-selected="false">
                  Declaration Forms
                </button>
              </li>
            </ul>
            <div class="tab-content pt-4" id="profileTabContent">
              <div class="tab-pane fade show active" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab"
                tabindex="0">
                <LearnerProfile :learnerDetails="learnerDetails" />
              </div>
              <div class="tab-pane fade" id="games-sessions-tab-pane" role="tabpanel"
                aria-labelledby="games-sessions-tab" tabindex="0">
                <Sessions :sortedSessions="sortedSessions" />
              </div>
              <div class="tab-pane fade" id="services-tab-pane" role="tabpanel" aria-labelledby="services-tab"
                tabindex="0">
                <Services :learnerDetails="learnerDetails" />
              </div>
              <div class="tab-pane fade" id="declaration-tab-pane" role="tabpanel" aria-labelledby="declaration-tab"
                tabindex="0">
                <DeclarationForms :learnerDetails="learnerDetails" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import DeclarationForms from "./DeclarationForms.vue";
import LearnerProfile from "./LearnerProfile.vue";
import Services from "./Services.vue";
import Sessions from "./Sessions.vue";

export default {
  name: "LearnerDashboard",
  components: {
    LearnerProfile,
    Sessions,
    Services,
    DeclarationForms
  },
  data() {
    return {
      learnerDetails: null,
      userData: null,
    };
  },
  computed: {
    speechLanguageDiagnosisList() {
      return this.$store.state.speechLanguageDiagnosisList;
    },
    sortedSessions() {
      return this.learnerDetails.learner_data.sessions.sort((a, b) => {
        return new Date(b.session_start_time) - new Date(a.session_start_time);
      });
    },
  },
  mounted() {
    this.getLearnerData();
  },
  methods: {
    async getLearnerData() {
      let learner_id = this.$route.params.id;
      try {
        const response = await axios.get(this.getUrl(learner_id), {
          headers: {
            Authorization: "Bearer " + this.$store.state.token,
          },
        });
        if (response && response.status == 200) {
          this.learnerDetails = response.data.data;
          // console.log(this.learnersData);
        }
      } catch (error) {
        console.error("Error uploading file:", error);
      }
    },
    getUrl(learner_id) {
      if (this.$store.state.user.role_slug === "clinician") {
        return "/api/learner/" + learner_id;
      } else if (this.$store.state.user.role_slug === "parents") {
        return (
          "/api/parents/" + this.$store.state.providerData.id + "/learner/" + learner_id
        );
      }
    },
    getDiagnosisValue(value) {
      if (value != "") {
        value = this.speechLanguageDiagnosisList.filter((diagnosis) => {
          return diagnosis.slug === value;
        })[0].name;
      }
      return value;
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
<style></style>
