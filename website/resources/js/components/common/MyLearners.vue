<template>
  <section class="mt-4">
    <section v-if="showMyLearnerTable">
      <div class="d-flex justify-content-between">
        <input type="search" v-model="searchQuery" class="form-control search" :placeholder="$t('Search')" />
        <div>
          <div class="d-inline-block me-3" @click="getLearnersData">
            <i class="fa fa-refresh" title="Refresh"></i>
          </div>
          <button type="button" v-if="userData.user.role_slug === 'clinician'" class="btn btn-outline-success"
            @click="addLearner">
            <i class="fas fa-plus"></i> {{ $t("Add Learner") }}
          </button>
          <button type="button" v-if="userData.user.role_slug === 'parents'" class="btn btn-outline-success"
            @click="addService">
            <i class="fas fa-plus"></i> {{ $t("Add Service") }}
          </button>
        </div>
      </div>
      <table v-if="learnersData" class="table table-striped table-bordered table-responsive mt-3">
        <thead>
          <tr>
            <th>{{ $t("fullname") }}</th>
            <th>{{ $t("learner_age") }}</th>
            <th>{{ $t("gender") }}</th>
            <th>{{ $t("grade") }}</th>
            <th>{{ $t("culture") }}</th>
            <th>{{ $t("diagnosis") }}</th>
            <th v-if="userData.user.role_slug === 'parents'">
              {{ $t("clinician_name") }}
            </th>
            <th>{{ $t("target_sound") }}</th>
            <th>{{ $t("last_game_played") }}</th>
            <th>{{ $t("history_of_played_games") }}</th>
            <th v-if="userData.user.role_slug === 'clinician' && isProfessionalPlusPlan">
              {{ $t("homework_helper") }}
            </th>
            <th v-if="userData.user.role_slug === 'clinician'">
              {{ $t("parent_approval_received") }}
            </th>
            <th v-if="userData.user.role_slug === 'parents'" style="width: 100px">{{ $t("action") }}</th>
          </tr>
        </thead>
        <tbody>
          <template v-if="resultQuery.length > 0">
            <tr v-for="(learnerData, index) in resultQuery" :key="index + 1">
              <td :class="isNewLearner(learnerData.learner_data) ? 'new-learner' : ''">
                <router-link :to="'/learner-dashboard/' + learnerData.learner_data.id"
                  @click="isNewLearner(learnerData.learner_data) ? closeNewBadge(learnerData.learner_data.id) : null">
                  {{ getFullName(learnerData.learner_data.user.fullname) }}</router-link>
                <span class="badge bg-danger" v-if="isNewLearner(learnerData.learner_data)">new</span>
              </td>
              <td :class="isNewLearner(learnerData.learner_data) ? 'new-learner' : ''">
                {{ learnerData.learner_data.learner_age }}
              </td>
              <td :class="isNewLearner(learnerData.learner_data) ? 'new-learner' : ''">
                {{ learnerData.learner_data.gender }}
              </td>
              <td :class="isNewLearner(learnerData.learner_data) ? 'new-learner' : ''">
                {{ learnerData.learner_data.grade }}
              </td>
              <td :class="isNewLearner(learnerData.learner_data) ? 'new-learner' : ''">
                {{
                  learnerData.learner_encrypt_data.culture != ""
                    ? $t(learnerData.learner_encrypt_data.culture)
                    : ""
                }}
              </td>
              <td :class="isNewLearner(learnerData.learner_data) ? 'new-learner' : ''">
                {{
                  getDiagnosisValue(
                    learnerData.learner_encrypt_data.speech_language_diagnosis
                  ) != ""
                    ? $t(
                      getDiagnosisValue(
                        learnerData.learner_encrypt_data.speech_language_diagnosis
                      )
                    )
                    : ""
                }}
              </td>
              <td :class="isNewLearner(learnerData.learner_data) ? 'new-learner' : ''"
                v-if="userData.user.role_slug === 'parents'">
                <table>
                  <tbody>
                    <tr
                      v-for="(clinicianName, clinicianNameIndex) in getClinicianNames(learnerData.learner_data.clinicians_data)"
                      :key="clinicianNameIndex">
                      <td class="nav"
                        :class="(clinicianNameIndex + 1) < getClinicianNames(learnerData.learner_data.clinicians_data).length ? 'bb-color' : ''">
                        {{ clinicianName.clinician_name }} <span class="service-name"
                          v-if="clinicianName.clinician_service">Service Name: {{
                            clinicianName.clinician_service ===
                              'mini-artic-assessment' ? 'Mini Artic Assessment' :
                              (clinicianName.clinician_service === 'mini-literacy-diagnostic' ? 'Mini Literacy Diagnostic' :
                                (clinicianName.clinician_service === 'homework-helper' ? 'Homework Helper' :
                                  '')) }}</span></td>
                    </tr>
                  </tbody>
                </table>
              </td>
              <td :class="isNewLearner(learnerData.learner_data) ? 'new-learner' : ''">
                <template v-if="learnerData.learner_data.sessions.length > 0">
                  <div v-for="(gameSession, gameIndex) in sortedSessions(learnerData.learner_data.sessions).slice(-2)"
                    :key="gameSession.id">
                    <label>
                      <b>Session {{ learnerData.learner_data.sessions.length - gameIndex }}: </b>
                    </label>
                    <p>{{ gameSession.target_sound }}</p>
                  </div>
                  <router-link :to="'/learner-dashboard/' + learnerData.learner_data.id"
                    class="link-primary custom-link"
                    v-if="sortedSessions(learnerData.learner_data.sessions).length > 2">Show
                    More</router-link>
                </template>
              </td>
              <td :class="isNewLearner(learnerData.learner_data) ? 'new-learner' : ''">
                <div v-if="learnerData.learner_data.sessions.length > 0">
                  {{
                    learnerData.learner_data.sessions[
                      learnerData.learner_data.sessions.length - 1
                    ].session_games[
                      learnerData.learner_data.sessions[
                        learnerData.learner_data.sessions.length - 1
                      ].session_games.length - 1
                    ].game_name
                  }}
                </div>
              </td>
              <td :class="isNewLearner(learnerData.learner_data) ? 'new-learner' : ''">
                <template v-if="learnerData.learner_data.sessions.length > 0">
                  <div
                    v-for="(gameSession, gameSessionIndex) in sortedSessions(learnerData.learner_data.sessions).slice(-2)"
                    :key="gameSession.id">
                    <label>
                      <b>Session {{ learnerData.learner_data.sessions.length - gameSessionIndex }}: </b>
                    </label>
                    <ul>
                      <li v-for="game in gameSession.session_games" :key="game.id">
                        {{ game.game_name }}
                      </li>
                    </ul>
                  </div>
                  <router-link :to="'/learner-dashboard/' + learnerData.learner_data.id"
                    class="link-primary custom-link"
                    v-if="sortedSessions(learnerData.learner_data.sessions).length > 2">Show
                    More</router-link>
                </template>
              </td>
              <td :class="isNewLearner(learnerData.learner_data)
                ? 'new-learner text-center'
                : 'text-center'
                " v-if="userData.user.role_slug === 'clinician' && isProfessionalPlusPlan">
                <label class="switch">
                  <input type="checkbox" :checked="learnerData.homework_helper_flag === '1'"
                    @change="changesHomeworkHelper($event, learnerData.id)" />
                  <span class="slider round"></span>
                </label>
              </td>
              <td :class="isNewLearner(learnerData.learner_data) ? 'new-learner' : ''"
                v-if="userData.user.role_slug === 'clinician'">
                -
              </td>
              <td :class="isNewLearner(learnerData.learner_data)
                ? 'new-learner text-center'
                : 'text-center'
                " v-if="userData.user.role_slug === 'parents'">
                <a class="btn btn-primary btn-sm" href="#" :title="$t('Edit Learner')"
                  @click="editLearner(learnerData)"><i class="fas fa-edit"></i>
                  Edit</a>
                <!-- <a href="#" :title="$t('Add Service')" @click="chooseService($event, learnerData)"><i
                    class="fas fa-wrench"></i></a> -->
                <!-- <a href="#" v-if="userData.user.role_slug === 'parents'" style="margin-left: 5px"
                  :title="$t('Clinician Learner Link')" @click="
                    showClinicianLearnerLinkModal(
                      learnerData.learner_data.id,
                      learnerData.learner_encrypt_data.id
                    )
                    "><i class="fas fa-solid fa-link"></i></a> -->
              </td>
            </tr>
          </template>
          <template v-else>
            <tr>
              <td class="text-center" colspan="17">No records found</td>
            </tr>
          </template>
        </tbody>
      </table>
      <nav class="mt-4" aria-label="Pagination" v-if="pagination.total && pagination.total > 10">
        <ul class="pagination justify-content-center">
          <li class="page-item" v-for="link in pagination.links" :key="link.label"
            :class="{ active: link.active, disabled: !link.url }">
            <a class="page-link" href="#" @click.prevent="link.url && getLearnersData(getPageNumber(link.url))" v-html="link.label === previousLabel || link.label === nextLabel
              ? $t(link.label)
              : link.label
              ">
            </a>
          </li>
        </ul>
      </nav>
    </section>
    <section v-if="addLearnerFormShow">
      <AddLearner ref="addLearnerRef" :userData="userData" @goback="goBack" />
    </section>
    <section v-if="editLearnerFormShow">
      <EditLearner ref="editLearnerRef" :learnerData="editLearnerData" :parent_id="parent_id" @goback="goBack" />
    </section>
    <section v-if="showModal">
      <ClinicianLearnerLinkModal :showModal="showModal" :learner_id="learner_id"
        :learner_encrypt_data_id="learner_encrypt_data_id" :parent_id="parent_id"
        @closeclinicianmodal="closeClinicianModal" />
    </section>
  </section>
</template>

<script>
import moment from "moment-timezone";
import axios from "../../axios";
import AddLearner from "./AddLearner.vue";
import ClinicianLearnerLinkModal from "../parents/ClinicianLearnerLinkModal.vue";
import EditLearner from "../parents/EditLearner.vue";
export default {
  props: ["userData"],
  emits: ["removeNewLearnerId"],
  components: {
    AddLearner,
    EditLearner,
    ClinicianLearnerLinkModal,
  },
  data() {
    return {
      learnersData: null,
      searchQuery: null,
      showMyLearnerTable: true,
      addLearnerFormShow: false,
      editLearnerFormShow: false,
      showModal: false,
      learner_id: null,
      learner_encrypt_data_id: null,
      parent_id: null,
      editLearnerData: null,
      // newLearnerIds: [],
      originalTime: "2025-01-01T13:30:00",
      convertedTime: "",
      pagination: {
        current_page: 1,
        last_page: 1,
        links: [],
        data: [],
      },
      previousLabel: "&laquo; Previous",
      nextLabel: "Next &raquo;",
    };
  },
  computed: {
    resultQuery() {
      if (this.searchQuery && this.searchQuery != "") {
        return this.learnersData.filter((learner) => {
          return this.searchQuery
            .toLowerCase()
            .split(" ")
            .every((v) =>
              Object.keys(learner).includes("learner_data")
                ? learner.learner_data.user.fullname.toLowerCase().includes(v)
                : ""
            );
        });
      } else {
        return this.learnersData;
      }
    },
    isProfessionalPlusPlan() {
      return (
        this.userData.user.plans.filter((plan) => {
          return plan.plan.name === "Professional Plus";
        }).length > 0
      );
    },
    speechLanguageDiagnosisList() {
      return this.$store.state.speechLanguageDiagnosisList;
    },
  },
  mounted() {
    this.getLearnersData();
    // this.newLearnerIds = this.$ls.get("newLearnerIds");
  },
  methods: {
    async getLearnersData(page = 1) {
      try {
        const response = await axios.get(this.getUrl(page), {
          headers: {
            Authorization: "Bearer " + this.$store.state.token,
          },
        });
        if (response && response.status == 200) {
          this.learnersData = response.data.data;
          this.pagination = response.data.pagination;
        }
      } catch (error) {
        console.error("Error uploading file:", error);
      }
    },
    async changesHomeworkHelper(e, id) {
      e.preventDefault();
      let homeworkHelperFlag = e.target.checked ? 1 : 0;
      try {
        const response = await axios.put(
          "/api/learner/update-homework-helper/" + id,
          {
            tab_id: "my-learners",
            homework_helper_flag: homeworkHelperFlag,
          },
          {
            headers: {
              Authorization: "Bearer " + this.$store.state.token,
            },
          }
        );
        if (response && response.status == 200) {
          // console.log(response);
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
    getUrl(page) {
      if (this.userData.user.role_slug === "clinician") {
        return "/api/learner/?clinician_id=" + this.userData.id + "&page=" + page;
      } else if (this.userData.user.role_slug === "parents") {
        return "/api/learner/parents/" + this.userData.id + "/?page=" + page;
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
    addLearner() {
      this.showMyLearnerTable = false;
      this.addLearnerFormShow = true;
    },
    editLearner(value) {
      this.parent_id = this.userData.id;
      this.editLearnerData = value;
      this.showMyLearnerTable = false;
      this.editLearnerFormShow = true;
    },
    addService(e) {
      e.preventDefault();
      this.$router.push({
        path: "/price",
        query: { parent_id: this.userData.id },
      });
    },
    chooseService(e, value) {
      e.preventDefault();
      this.$router.push({
        path: "/price",
        query: { parent_id: this.userData.id, learner_id: value.learner_data.id },
      });
    },
    goBack() {
      this.showMyLearnerTable = true;
      this.addLearnerFormShow = false;
      this.editLearnerFormShow = false;
    },
    showClinicianLearnerLinkModal(learner_id, learner_encrypt_data_id) {
      this.learner_id = learner_id;
      this.learner_encrypt_data_id = learner_encrypt_data_id;
      this.parent_id = this.userData.id;
      this.showModal = true;
    },
    closeClinicianModal() {
      this.showModal = false;
    },
    async closeNewBadge(learnerId) {
      try {
        const response = await axios.put(
          "/api/user/" + learnerId + "/update-viewed",
          {
            assessment_viewed: 0,
            literacy_viewed: 0,
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
        }
      } catch (error) {
        console.error("Error uploading file:", error);
      }
    },
    getPageNumber(url) {
      const params = new URLSearchParams(url.split("?")[1]);
      return params.get("page");
    },
    isNewLearner(learnerData) {
      if (this.$store.state.providerData) {
        let clinician = learnerData.clinicians_data.filter(clinician => {
          return clinician.clinician_learner_link_data.clinician_id == this.$store.state.providerData.id;
        });
        if (clinician.length > 0) {
          let clinicianData = clinician[0];
          return this.userData.user.role_slug === "clinician" && (clinicianData.clinician_learner_link_data.assessment_viewed === 1 || clinicianData.clinician_learner_link_data.literacy_viewed === 1 || clinicianData.clinician_learner_link_data.homework_helper_viewed === 1);
        }
      }
    },
    sortedSessions(learnerDataSessions) {
      return learnerDataSessions.sort((a, b) => {
        return new Date(b.session_start_time) - new Date(a.session_start_time);
      });
    },
    getClinicianNames(clinicians_data) {
      let clinicianNames = [];
      clinicians_data.forEach((clinician) => {
        clinicianNames.push({ clinician_name: clinician.clinician_data.user.fullname, clinician_service: clinician.clinician_learner_link_data.service_type });
      });
      return clinicianNames;
    }
  },
  watch: {
    addLearnerFormShow(value) {
      if (!value) {
        this.getLearnersData();
      }
    },
    editLearnerFormShow(value) {
      if (!value) {
        this.getLearnersData();
      }
    },
    showModal(value) {
      if (!value) {
        this.getLearnersData();
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

.search {
  width: 25%;
}

a {
  text-decoration: none;
}

.fa-link {
  color: lightseagreen;
}

.new-learner {
  background-color: lightgreen;
}

.fa-arrows-rotate {
  cursor: pointer;
}

.fa-wrench {
  color: brown;
}

.custom-link {
  font-size: 15px;
  cursor: pointer;
}

.bb-color {
  border-bottom: 1px solid #e6e6e6;
}

.service-name {
  font-size: 14px;
  font-weight: 600;
}
</style>
