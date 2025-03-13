<template>
  <div class="container stepper-container">
    <div class="stepper">
      <!-- Stepper Header -->
      <div class="steps">
        <div v-for="(stepItem, index) in steps" :key="index" :class="[
          'step',
          { active: currentStep === index + 1, completed: currentStep > index + 1 },
        ]">
          <!--  @click="goToStep(index + 1)" -->
          <b>{{ stepItem.label }}</b>
        </div>
      </div>

      <!-- Stepper Content -->
      <div class="content">
        <div v-for="stepItem in steps" :key="stepItem.id">
          <component :is="stepItem.component" v-if="stepItem.showIf" />
        </div>
        <div class="nav mt-2" :class="currentStep === 1 ? 'justify-content-end' : 'justify-content-between'">
          <button class="btn btn-secondary" v-if="currentStep > 1" @click="goToStep('prev')">
            Back
          </button>
          <button class="btn btn-primary" v-if="currentStep < steps.length" @click="goToStep('next')">
            Next
          </button>
          <button class="btn btn-primary" v-else @click="finishStepper">Finish</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import StepOne from "../components/StepOne.vue";
import StepThree from "../components/StepThree.vue";
import StepTwo from "../components/StepTwo.vue";

export default {
  name: "ChooseLearner",
  components: {
    StepOne,
    StepTwo,
    StepThree,
  },
  data() {
    return {
      numServices: "",
      services: [],
      updatedservices: [],
      learnersData: null,
      selectedServices: [],
      formData: {
        learner_id: "",
        learner_name: "",
        learner_age: "",
        preferred_language: "",
        additional_comments: "",
        selectedService: "",
        learnerUniqueId: "",
      },
      currentStep: 1,
      steps: [
        {
          id: "step-one",
          name: "StepOne",
          step: 1,
          label: "Step 1 - Choose Learner",
          component: "StepOne",
          showIf: true,
        },
        {
          id: "step-two",
          name: "StepTwo",
          step: 2,
          label: "Step 2 - Add Additional Information",
          component: "StepTwo",
          showIf: false,
        },
        {
          id: "step-three",
          name: "StepThree",
          label: "Step 3 - Schedule Meeting",
          component: "StepThree",
          showIf: false,
        },
      ],
    };
  },
  computed: {
    learnerAges() {
      return this.$store.state.learnerAges;
    },
    numberOfServices() {
      return Array.from({ length: this.$store.state.cart.length }, (_, i) => i + 1);
    },
    servicesList() {
      return this.$store.state.cart;
      // return this.$store.state.cart.flatMap((service) =>
      //     Array(service.quantity).fill({ ...service, quantity: 1 })
      // );
    },
  },
  mounted() {
    // console.log(this.servicesList);
    this.updatedservices = this.servicesList;
    // this.getLearnersByParentsId();
  },
  methods: {
    async getLearnersByParentsId() {
      try {
        const response = await axios.get(
          "/api/learner/parents/" + this.$route.query.parent_id + "/?page=all",
          {
            headers: {
              Authorization: "Bearer " + this.$store.state.token,
            },
          }
        );
        if (response && response.status == 200) {
          this.learnersData = response.data.data;
          // console.log(this.learnersData);
        }
      } catch (error) {
        console.error("Error uploading file:", error);
      }
    },
    goToStep(step) {
      if (step === "prev") {
        this.currentStep--;
      } else if (step === "next") {
        this.currentStep++;
      } else {
        this.currentStep = step;
      }
      this.steps.forEach((step) => (step.showIf = false));
      this.steps[this.currentStep - 1].showIf = true;
    },
    finishStepper() {
      alert("Stepper Finished!");
    },
    generateUniqueId() {
      const name = this.formData.learner_name || this.formData.learner_id;
      if (name) {
        const formattedName = name.replace(/\s+/g, "-").toLowerCase();
        this.formData.learnerUniqueId = `${formattedName}-${Date.now()}`;
      } else {
        this.formData.learnerUniqueId = "";
      }
    },
  },
};
</script>

<style scoped>
.stepper {
  /* width: 300px; */
  margin: auto;
}

.steps {
  display: flex;
  justify-content: space-between;
  margin-bottom: 20px;
}

.step {
  width: 30%;
  text-align: center;
  padding: 10px;
  border: 1px solid #ccc;
  cursor: pointer;
  border-radius: 5px;
}

.step.active {
  background-color: #0d6efd;
  color: white;
}

.step.completed {
  background-color: #ccc;
  color: white;
}

.content {
  /* text-align: center; */
}

button {
  margin: 5px;
}
</style>
