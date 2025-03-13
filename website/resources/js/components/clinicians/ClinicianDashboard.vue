<template>
  <section class="card card-body shadow-sm mt-2">
    <ul class="nav nav-tabs" id="myTab" role="tablist">
      <li class="nav-item" role="presentation" v-for="tab in tabs" :key="tab.id">
        <button class="nav-link" :class="{ active: activeTab === tab.id }" :id="tab.id + '-tab'" data-bs-toggle="tab"
          :data-bs-target="'#' + tab.id" type="button" role="tab" :aria-controls="tab.id"
          :aria-selected="activeTab === tab.id" @click="setActiveTab(tab.id)">
          {{ $t(tab.name) }}
        </button>
      </li>
    </ul>
    <div class="tab-content" id="myTabContent">
      <div v-for="tab in tabs" :key="tab.id" class="tab-pane fade"
        :class="{ show: activeTab === tab.id, active: activeTab === tab.id }" :id="tab.id" role="tabpanel"
        :aria-labelledby="tab.id + '-tab'">
        <component :is="tab.component" v-if="tab.showIf && clinicianData" :userData="clinicianData"
          @navigate="setActiveTab" @removeNewLearnerId="removeNewLearnerIdBadge" ref="activeComponent" />
      </div>
    </div>
  </section>
  <Toaster ref="toasterRef" />
</template>

<script>
import moment from "moment";
import LatestAction from "../common/LatestAction.vue";
import MyLearners from "../common/MyLearners.vue";
import DetailedLearnerReport from "../common/DetailedLearnerReport.vue";
import HomeworkHelper from "../common/HomeworkHelper.vue";
import SystemFunctions from "./SystemFunctions.vue";
import WhatsNew from "../common/WhatsNew.vue";
import Assessment from "../common/Assessment.vue";
import Toaster from "../common/Toaster.vue";
import pusher from "../../pusher";
import LiteracyDiagnostic from "../common/LiteracyDiagnostic.vue";

export default {
  name: "ClinicianDashboard",
  components: {
    LatestAction,
    MyLearners,
    DetailedLearnerReport,
    HomeworkHelper,
    Assessment,
    LiteracyDiagnostic,
    SystemFunctions,
    WhatsNew,
    Toaster,
  },
  data() {
    return {
      clinicianData: null,
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
        {
          id: "system-function",
          name: "System Functions",
          component: "SystemFunctions",
          showIf: true,
        },
        { id: "whats-new", name: "What's New", component: "WhatsNew", showIf: true },
      ],
      newLearnerAdded: false,
      // newLearnerIds: [],
    };
  },
  mounted() {
    this.getClinicianData();
    this.initPusherNotification();
    // Ensure correct tab is active on page load
    this.$nextTick(() => {
      const tabElement = document.querySelector(`#${this.activeTab}-tab`);
      if (tabElement) tabElement.click();
    });
    // if (this.$ls.get("newLearnerIds")) this.newLearnerIds = this.$ls.get("newLearnerIds");
  },
  methods: {
    async getClinicianData() {
      await this.$store.dispatch("getProviderData", "clinician");
      this.clinicianData = this.$store.state.providerData;
      // this.$store.state.providerData.plan = "upgrade";
      // console.log(this.$store.state.providerData)
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
    initPusherNotification() {
      // Subscribe to a channel
      const channel = pusher.subscribe("send-notification-to-clinician");

      // Listen for an event
      channel.bind("new-learner", (data) => {
        if (data.data.clinician_id === this.clinicianData.id) {
          // this.newLearnerIds.push(data.data.learner_id);
          // this.$ls.set("newLearnerIds", this.newLearnerIds);
          this.newLearnerAdded = true;
          if (this.$refs.toasterRef)
            this.$refs.toasterRef.addToast("Notification", data.data.message);
        }
      });
    },
    removeNewLearnerIdBadge(learnerId) {
      // this.newLearnerIds.splice(this.newLearnerIds.indexOf(learnerId), 1);
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
              this.clinicianData.user.role_slug +
              "/latest-actions/" +
              this.clinicianData.id
            );
          }
        } else if (newTab === "my-learners") {
          // Ensure Latest Action tab is active
          const latestActionComponent = this.$refs.activeComponent;
          if (latestActionComponent) {
            latestActionComponent[1].getLearnersData();
          }
        } else if (newTab === "homework-helper") {
          const latestActionComponent = this.$refs.activeComponent;
          if (latestActionComponent) {
            latestActionComponent[3].getLearnerDetails();
          }
        } else if (newTab === "assessment") {
          const latestActionComponent = this.$refs.activeComponent;
          if (latestActionComponent) {
            latestActionComponent[4].getLearnerDetails();
          }
        }
      }
    },
    newLearnerAdded(value) {
      if (value) {
        const myLearnersComponent = this.$refs.activeComponent;
        if (myLearnersComponent) {
          myLearnersComponent[1].getLearnersData();
          myLearnersComponent[4].getLearnerDetails();
          // console.log(this.$ls.get("newLearnerIds"));
          // myLearnersComponent[1].newLearnerIds = this.$ls.get("newLearnerIds");
        }
        this.newLearnerAdded = false;
      }
    },
  },
};
</script>
