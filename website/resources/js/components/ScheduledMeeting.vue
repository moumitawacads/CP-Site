<template>
  <div class="container mt-5">
    <div class="card" v-if="userData">
      <div class="card-body text-center p-rem-4">
        <div class="row">
          <div class="col-md-12 d-flex justify-content-center">
            <div class="div-icon" :class="isScheduled ? 'succes' : 'error'">
              <i class="fas fa-regular" :class="isScheduled ? 'fa-calendar-check' : 'fa-calendar-xmark'"></i>
            </div>
          </div>
        </div>
        <div class="row mt-3">
          <div class="col-md-12">
            <h3>Your Meeting has {{ isScheduled ? "" : "not" }} been Scheduled</h3>
          </div>
        </div>
        <div class="row mt-4">
          <div class="col-md-12">
            <p style="font-size: 15px" v-if="isScheduled">
              Thank you for your appointment request. Well will contact you shortly to
              confirm your request. Please call our office at (215) 421-4096 if you have
              any question.
            </p>
            <p style="font-size: 15px" v-else>
              We are sorry, we could not schedule your meeting because your email address
              mismatch. Please reschedule the meeting again.
            </p>
          </div>
        </div>
        <div class="row d-flex justify-content-center mt-5" v-if="isScheduled">
          <div class="col-md-6">
            <div class="card">
              <div class="card-body" style="text-align: left">
                <div class="row">
                  <div class="col-1">
                    <i class="fas fa-regular fa-clock"></i>
                  </div>
                  <div class="col-11">
                    <p>30 Mins</p>
                  </div>
                </div>
                <div class="row">
                  <div class="col-1">
                    <i class="fas fa-regular fa-calendar"></i>
                  </div>
                  <div class="col-11">
                    <p>{{ formattedMeetingTime }}</p>
                  </div>
                </div>
                <div class="row">
                  <div class="col-1">
                    <i class="fas fa-solid fa-location-dot"></i>
                  </div>
                  <div class="col-11">
                    <p>{{ isScheduled.calendar.address }}</p>
                  </div>
                </div>
                <div class="row">
                  <div class="col-1">
                    <i class="fas fa-solid fa-globe"></i>
                  </div>
                  <div class="col-11">
                    <p>{{ location }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row mt-5" v-else>
          <div class="col-md-12">
            <a href="/calendar" class="btn btn-primary">Back to Calendar</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import moment from "moment-timezone";

export default {
  name: "ScheduledMeeting",
  data() {
    return {
      responseData: null,
      userData: null,
    };
  },
  computed: {
    isScheduled() {
      return this.userData.calendar_webhook_response
        ? JSON.parse(this.userData.calendar_webhook_response)
        : null;
    },
    formattedMeetingTime() {
      if (this.isScheduled) {
        const timezone = this.isScheduled.timezone;
        const eastIndianaStartTime = moment
          .tz(
            this.isScheduled.calendar.startTime,
            this.isScheduled.calendar.selectedTimezone
          )
          .format();
        const eastIndianaEndTime = moment
          .tz(
            this.isScheduled.calendar.endTime,
            this.isScheduled.calendar.selectedTimezone
          )
          .format();

        const startTime = moment.tz(eastIndianaStartTime, this.isScheduled.timezone);
        const endTime = moment.tz(eastIndianaEndTime, this.isScheduled.timezone);
        return `${startTime.format("hh:mm A")} - ${endTime.format(
          "hh:mm A"
        )}, ${startTime.format("ddd, MMM DD, YYYY")}`;
      }
      return "";
    },
    location() {
      if (this.isScheduled) {
        const timezone = this.isScheduled.timezone;
        return `${timezone} (GMT${moment.tz(timezone).format("Z")})`;
      }
      return "";
    },
  },
  mounted() {
    this.responseData = this.$ls.get("responseData");
    if (this.responseData) this.getCalendarWebhookResponse();
    else this.$router.push({ name: "home" });
    // console.log(this.convertTime);
  },
  methods: {
    async getCalendarWebhookResponse() {
      const response = await axios.get(
        "/api/user/" + this.responseData.user.id + "/calendar-webhook-response"
      );
      if (response && response.status === 200) {
        this.userData = response.data.user;
        if (this.isScheduled) {
          this.setToken(this.responseData);
          setTimeout(() => {
            this.$router.push({ name: "dashboard" }); // Adjust route as per your setup
          }, 4000);
        }
      }
    },
    setToken(responseData) {
      this.$store.dispatch("login", responseData);
      this.$i18n.locale = responseData.user.preferred_language;
    },
  },
};
</script>

<style scoped>
.p-rem-4 {
  padding: 1rem 4rem;
}

.div-icon {
  width: 70px;
  height: 70px;
  border-radius: 50%;
  display: flex;
  justify-content: center;
  align-items: center;
}

.succes {
  background-color: springgreen;
}

.error {
  background-color: #dc3545;
}

.fa-calendar-check {
  height: 30px;
}

.fa-calendar-xmark {
  height: 30px;
}
</style>
