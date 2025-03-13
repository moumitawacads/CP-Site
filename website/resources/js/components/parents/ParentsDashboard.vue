<template>
  <section class="card card-body shadow-sm mt-2" v-if="parentsData">
    <ul class="nav nav-tabs" id="myTab" role="tablist">
      <li class="nav-item" role="presentation" v-for="tab in tabs" :key="tab.id">
        <button class="nav-link" :class="{ active: activeTab === tab.id }" :id="tab.id + '-tab'" data-bs-toggle="tab"
          v-if="tab.showIf" :data-bs-target="'#' + tab.id" type="button" role="tab" :aria-controls="tab.id"
          :aria-selected="activeTab === tab.id" @click="setActiveTab(tab.id)">
          {{ $t(tab.name) }}
        </button>
      </li>
    </ul>
    <div class="tab-content" id="myTabContent">
      <div v-for="tab in tabs" :key="tab.id" class="tab-pane fade"
        :class="{ show: activeTab === tab.id, active: activeTab === tab.id }" :id="tab.id" role="tabpanel"
        :aria-labelledby="tab.id + '-tab'">
        <component :is="tab.component" v-if="tab.showIf && parentsData" :userData="parentsData" @navigate="setActiveTab"
          ref="activeComponent" />
      </div>
    </div>
  </section>
</template>

<script>
import moment from "moment";
import LatestAction from "../common/LatestAction.vue";
import MyLearners from "../common/MyLearners.vue";
import DetailedLearnerReport from "../common/DetailedLearnerReport.vue";
import HomeworkHelper from "../common/HomeworkHelper.vue";
// import SystemFunctions from "./SystemFunctions.vue";
import WhatsNew from "../common/WhatsNew.vue";
import Assessment from "../common/Assessment.vue";
import LiteracyDiagnostic from "../common/LiteracyDiagnostic.vue";

export default {
  name: "ParentsDashboard",
  components: {
    LatestAction,
    MyLearners,
    DetailedLearnerReport,
    HomeworkHelper,
    Assessment,
    LiteracyDiagnostic,
    // SystemFunctions,
    WhatsNew,
  },
  data() {
    return {
      parentsData: null,
      activeTab: this.$ls.get("activeTab") || "latest-action", // Retrieve from localStorage or set default
      tabs: [
        {
          id: "latest-action",
          name: "Latest Action",
          component: "LatestAction",
          showIf: true,
        },
        { id: "my-learners", name: "My Learners", component: "MyLearners", showIf: true },
        {
          id: "detailed-learner-report",
          name: "Detailed Learner Report",
          component: "DetailedLearnerReport",
          showIf: true,
        },
        {
          id: "homework-helper",
          name: "Homework Helper",
          component: "HomeworkHelper",
          showIf: true,
        },
        {
          id: "assessment",
          name: "Assessment",
          component: "Assessment",
          showIf: true,
        },
        {
          id: "literacy-diagnostic",
          name: "Literacy Diagnostic",
          component: "LiteracyDiagnostic",
          showIf: true,
        },
        // {
        //   id: "system-function",
        //   name: "System Functions",
        //   component: "SystemFunctions",
        //   showIf: true,
        // },
        { id: "whats-new", name: "What's New", component: "WhatsNew", showIf: true },
      ],
    };
  },
  mounted() {
    this.getParensData();
    // Ensure correct tab is active on page load
    this.$nextTick(() => {
      const tabElement = document.querySelector(`#${this.activeTab}-tab`);
      if (tabElement) tabElement.click();
    });
  },
  methods: {
    async getParensData() {
      await this.$store.dispatch("getProviderData", "parents");
      this.parentsData = this.$store.state.providerData;
    },
    formatDate(value) {
      if (value) {
        return moment(String(value)).format("DD/MM/YYYY hh:mm:ss");
      }
    },
    setActiveTab(tabId) {
      this.activeTab = tabId;
      this.$ls.set("activeTab", tabId); // Save the active tab in localStorage
    },
  },
  watch: {
    activeTab(newTab) {
      const tokenExpiry = this.$ls.get("token_expiry");
      if (!tokenExpiry || Date.now() > tokenExpiry) {
        this.$store.dispatch("logout");
      } else {
        if (newTab === "latest-action") {
          // Ensure Latest Action tab is active
          const latestActionComponent = this.$refs.activeComponent;
          if (latestActionComponent) {
            latestActionComponent[0].getLatestActions(
              "/api/" +
              this.parentsData.user.role_slug +
              "/latest-actions/" +
              this.parentsData.id
            );
          }
        } else if (newTab === "my-learners") {
          // Ensure Latest Action tab is active
          const latestActionComponent = this.$refs.activeComponent;
          if (latestActionComponent) {
            latestActionComponent[1].getLearnersData();
          }
        }
      }
    },
  },
};
</script>
