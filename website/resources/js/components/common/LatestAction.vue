<template>
  <section class="mt-4">
    <ul class="list-group list-group-flush">
      <li class="list-group-item" v-for="action in latestActionsData" :key="action.id">
        <a class="action-link" href="#" @click="navigateToTab(action.tab_id)">
          {{
            $t(
              action.lang_action_description,
              getTranslationParams(action.json_decoded_dynamic_value)
            )
          }}
        </a>
        <span class="action-time">
          {{
            $t(getTimeDes(action.created_at), {
              time: extractNumber(getTime(action.created_at)),
            })
          }}
        </span>
      </li>
    </ul>
    <nav class="mt-4" aria-label="Pagination" v-if="pagination.total && pagination.total > 10">
      <ul class="pagination justify-content-center">
        <li class="page-item" v-for="link in pagination.links" :key="link.label"
          :class="{ active: link.active, disabled: !link.url }">
          <a class="page-link" href="#" @click.prevent="link.url && getLatestActions(link.url)" v-html="link.label === previousLabel || link.label === nextLabel
            ? $t(link.label)
            : link.label
            ">
          </a>
        </li>
      </ul>
    </nav>
  </section>
</template>

<script>
import moment from "moment";
import "moment/locale/fr";
// import "moment/locale/en";
import axios from "../../axios";

export default {
  props: ["userData"],
  emits: ["removeNewLearnerId"],
  data() {
    return {
      latestActionsData: null,
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
  mounted() {
    // console.log(this.userData);
    this.getLatestActions(
      "/api/" + this.userData.user.role_slug + "/latest-actions/" + this.userData.id
    );
  },
  methods: {
    async getLatestActions(url) {
      try {
        const response = await axios.get(url, {
          headers: {
            Authorization: "Bearer " + this.$store.state.token,
          },
        });
        if (response && response.status == 200) {
          this.latestActionsData = response.data.data;
          this.pagination = response.data;
          // console.log(this.latestActionsData);
        }
      } catch (error) {
        console.error("Error uploading file:", error);
      }
    },
    getTranslationParams(dynamicValues) {
      const keysToCheck = ["learner_name", "clinician_name", "system_setting", "service_name"];
      const result = {};

      keysToCheck.forEach((key) => {
        if (dynamicValues.hasOwnProperty(key)) {
          if (key === "system_setting") {
            result[key] = this.$t(dynamicValues[key]);
          } else {
            result[key] = dynamicValues[key];
          }
        }
      });

      return result;
    },
    navigateToTab(tabId) {
      this.$emit("navigate", tabId); // Inform the parent to switch tabs
    },
    getTime(datetime) {
      return moment(datetime).fromNow();
    },
    extractNumber(timeString) {
      const match = timeString.match(/\d+/); // Match the first number in the string
      return match ? match[0] : null; // Return the number if found, otherwise null
    },
    getTimeDes(datetime) {
      if (moment(datetime).fromNow().includes("a few seconds ago")) {
        return "time.second";
      } else if (moment(datetime).fromNow().includes("a minute ago")) {
        return "time.minute";
      } else if (moment(datetime).fromNow().includes("minutes ago")) {
        return "time.minutes";
      } else if (moment(datetime).fromNow().includes("an hour ago")) {
        return "time.hour";
      } else if (moment(datetime).fromNow().includes("hours ago")) {
        return "time.hours";
      } else if (moment(datetime).fromNow().includes("a day ago")) {
        return "time.day";
      } else if (moment(datetime).fromNow().includes("days ago")) {
        return "time.days";
      } else if (moment(datetime).fromNow().includes("a month ago")) {
        return "time.month";
      } else if (moment(datetime).fromNow().includes("months ago")) {
        return "time.months";
      } else if (moment(datetime).fromNow().includes("a year ago")) {
        return "time.year";
      } else if (moment(datetime).fromNow().includes("years ago")) {
        return "time.years";
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

table,
th,
td {
  border: 1px solid;
}

a {
  text-decoration: none;
}

.action-link {
  display: inline-block;
  width: 82%;
}

.action-time {
  display: inline-flex;
  width: 18%;
  justify-content: flex-end;
  font-size: 14px;
}
</style>
