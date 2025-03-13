<template>
  <div
    v-if="isVirtualReadinessAssessmentModalOpen"
    class="modal-backdrop"
    @click="closeModal"
  ></div>
  <div
    v-if="isVirtualReadinessAssessmentModalOpen"
    class="modal d-block"
    tabindex="-1"
    style="z-index: 1050"
  >
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form
          class="needs-validation-virtual"
          novalidate
          ref="virtualReadinessForm"
          @submit.prevent="virtualReadinessSubmit"
        >
          <div class="modal-header">
            <h5 class="modal-title">Virtual Language Instruction Readiness Form</h5>
            <button type="button" class="btn-close" @click="closeModal"></button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-12 mb-3">
                <label class="form-label display-block"
                  >1. Does your child have access to a computer or tablet with a stable
                  internet connection?
                </label>
                <div class="form-check display-inline-block pl-rem">
                  <input
                    type="radio"
                    class="form-check-input"
                    id="stable-internet-yes"
                    name="stable-internet"
                    value="1"
                    v-model="formData.access_to_a_computer_or_tablet"
                    required
                  />
                  <label class="form-check-label" for="stable-internet-yes">Yes</label>
                </div>
                <div class="form-check display-inline-block ml-8">
                  <input
                    type="radio"
                    class="form-check-input"
                    id="stable-internet-no"
                    name="stable-internet"
                    value="0"
                    v-model="formData.access_to_a_computer_or_tablet"
                    required
                  />
                  <label class="form-check-label" for="stable-internet-no">No</label>
                </div>
              </div>
              <div class="col-md-12 mb-3">
                <label class="form-label display-block"
                  >2. Does your child have a headset or microphone for clear audio
                  communication?
                </label>
                <div class="form-check display-inline-block pl-rem">
                  <input
                    type="radio"
                    class="form-check-input"
                    id="clear-audio-yes"
                    name="clear-audio"
                    value="1"
                    v-model="formData.have_a_headset_or_microphone"
                    required
                  />
                  <label class="form-check-label" for="clear-audio-yes">Yes</label>
                </div>
                <div class="form-check display-inline-block ml-8">
                  <input
                    type="radio"
                    class="form-check-input"
                    id="clear-audio-no"
                    name="clear-audio"
                    value="0"
                    v-model="formData.have_a_headset_or_microphone"
                    required
                  />
                  <label class="form-check-label" for="clear-audio-no">No</label>
                </div>
              </div>
              <div class="col-md-12 mb-3">
                <label class="form-label display-block"
                  >3. Will an adult be available to assist your child during virtual
                  instruction sessions if needed?
                </label>
                <div class="form-check display-inline-block pl-rem">
                  <input
                    type="radio"
                    class="form-check-input"
                    id="virtual-instruction-yes"
                    name="virtual-instruction"
                    value="1"
                    v-model="formData.adult_be_available_to_assist_your_child"
                    required
                  />
                  <label class="form-check-label" for="virtual-instruction-yes"
                    >Yes</label
                  >
                </div>
                <div class="form-check display-inline-block ml-8">
                  <input
                    type="radio"
                    class="form-check-input"
                    id="virtual-instruction-no"
                    name="virtual-instruction"
                    value="0"
                    v-model="formData.adult_be_available_to_assist_your_child"
                    required
                  />
                  <label class="form-check-label" for="virtual-instruction-no">No</label>
                </div>
              </div>
              <div class="col-md-12 mb-3">
                <label class="form-label display-block"
                  >4. Is there a quiet, distraction-free space for your child to
                  participate in virtual lessons?
                </label>
                <div class="form-check display-inline-block pl-rem">
                  <input
                    type="radio"
                    class="form-check-input"
                    id="virtual-lessons-yes"
                    name="virtual-lessons"
                    value="1"
                    v-model="formData.distraction_free_space_for_your_child"
                    required
                  />
                  <label class="form-check-label" for="virtual-lessons-yes">Yes</label>
                </div>
                <div class="form-check display-inline-block ml-8">
                  <input
                    type="radio"
                    class="form-check-input"
                    id="virtual-lessons-no"
                    name="virtual-lessons"
                    value="0"
                    v-model="formData.distraction_free_space_for_your_child"
                    required
                  />
                  <label class="form-check-label" for="virtual-lessons-no">No</label>
                </div>
              </div>
              <div class="col-md-12 mb-3">
                <label class="form-label display-block"
                  >5. Is your child comfortable participating in interactive activities
                  (e.g., answering questions, clicking on buttons) ?
                </label>
                <div class="form-check display-inline-block pl-rem">
                  <input
                    type="radio"
                    class="form-check-input"
                    id="interactive-activities-yes"
                    name="interactive-activities"
                    value="1"
                    v-model="formData.comfortable_participating_in_interactive_activities"
                    required
                  />
                  <label class="form-check-label" for="interactive-activities-yes"
                    >Yes</label
                  >
                </div>
                <div class="form-check display-inline-block ml-8">
                  <input
                    type="radio"
                    class="form-check-input"
                    id="interactive-activities-no"
                    name="interactive-activities"
                    value="0"
                    v-model="formData.comfortable_participating_in_interactive_activities"
                    required
                  />
                  <label class="form-check-label" for="interactive-activities-no"
                    >No</label
                  >
                </div>
              </div>
            </div>
            <div class="form-check mt-5">
              <input
                class="form-check-input"
                type="checkbox"
                value=""
                id="virtual-readiness-consent"
                v-model="formData.virtualReadinessAssessment"
                @change="changeVirtualReadinessAssessment"
                required
              />
              <label class="form-check-label" for="virtual-readiness-consent"
                >By clicking here, I state that I have read and understood the terms and
                conditions.</label
              >
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" :disabled="disabledBtn" class="btn btn-primary">
              Submit
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import moment from "moment";

export default {
  name: "VirtualReadinessAssessment",
  props: ["isVirtualReadinessAssessmentModalOpen"],
  emits: [
    "closeVirtualReadinessAssessmentModal",
    "updateVirtualReadinessAssessmentFlag",
    "changeVirtualReadinessAssessment",
  ],
  data() {
    return {
      formData: {
        access_to_a_computer_or_tablet: null,
        have_a_headset_or_microphone: null,
        adult_be_available_to_assist_your_child: null,
        distraction_free_space_for_your_child: null,
        comfortable_participating_in_interactive_activities: null,
        virtualReadinessAssessment: null,
        virtualReadinessAssessmentDatetime: null,
      },
      disabledBtn: false,
      errorMsg: null,
    };
  },
  mounted() {
    this.setVRALocalStorageValues();
  },
  methods: {
    async virtualReadinessSubmit(event) {
      event.preventDefault();
      this.disabledBtn = true;
      let validation = this.checkForm(event);
      if (validation) {
        this.disabledBtn = false;
        this.$emit("updateVirtualReadinessAssessmentFlag", true);
        this.$ls.set("virtual_language_instruction_form", this.formData);
        this.closeModal(event);
      } else {
        this.disabledBtn = false;
      }
    },
    setVRALocalStorageValues() {
      if (this.$ls.get("virtual_language_instruction_form")) {
        let virtualFormData = this.$ls.get("virtual_language_instruction_form");
        this.formData.virtualReadinessAssessment =
          virtualFormData.virtualReadinessAssessment;
        this.formData.virtualReadinessAssessmentDatetime =
          virtualFormData.virtualReadinessAssessmentDatetime;
        this.formData.access_to_a_computer_or_tablet =
          virtualFormData.access_to_a_computer_or_tablet;
        this.formData.have_a_headset_or_microphone =
          virtualFormData.have_a_headset_or_microphone;
        this.formData.adult_be_available_to_assist_your_child =
          virtualFormData.adult_be_available_to_assist_your_child;
        this.formData.distraction_free_space_for_your_child =
          virtualFormData.distraction_free_space_for_your_child;
        this.formData.comfortable_participating_in_interactive_activities =
          virtualFormData.comfortable_participating_in_interactive_activities;
        this.$emit("updateVirtualReadinessAssessmentFlag", true);
      }
    },
    changeVirtualReadinessAssessment(e) {
      e.preventDefault();
      this.formData.virtualReadinessAssessment = e.target.checked
        ? e.target.checked
        : null;
      this.formData.virtualReadinessAssessmentDatetime = e.target.checked
        ? moment.utc().format("DD-MM-YYYY HH:mm:ss")
        : null;
    },
    checkForm(e) {
      if (
        this.formData.virtualReadinessAssessment != null &&
        this.formData.virtualReadinessAssessmentDatetime != null &&
        this.formData.access_to_a_computer_or_tablet != null &&
        this.formData.have_a_headset_or_microphone != null &&
        this.formData.adult_be_available_to_assist_your_child != null &&
        this.formData.distraction_free_space_for_your_child != null &&
        this.formData.comfortable_participating_in_interactive_activities != null
      )
        return true;
      let form = document.querySelector(".needs-validation-virtual");
      form.classList.add("was-validated");
      e.preventDefault();
      return false;
    },
    closeModal(e) {
      e.preventDefault();
      this.$emit("closeVirtualReadinessAssessmentModal");
    },
  },
  watch: {
    isVirtualReadinessAssessmentModalOpen(newVal) {
      if (newVal) {
        this.setVRALocalStorageValues();
      }
    },
  },
};
</script>

<style scoped>
.display-block {
  display: block;
}

.display-inline-block {
  display: inline-block;
}

.ml-8 {
  margin-left: 12px;
}

.pl-rem {
  padding-left: 2.5rem;
}

.modal-backdrop {
  background-color: rgba(0, 0, 0, 0.5);
  /* Adjust opacity if needed */
}
</style>
