<template>
  <div v-if="isGDPRModalOpen" class="modal-backdrop" @click="closeModal"></div>
  <div v-if="isGDPRModalOpen" class="modal d-block" tabindex="-1" style="z-index: 1050">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form
          class="needs-validation-gdpr"
          novalidate
          ref="gdprForm"
          @submit.prevent="gdprSubmit"
        >
          <div class="modal-header">
            <h5 class="modal-title">GDPR Permission</h5>
            <button type="button" class="btn-close" @click="closeModal"></button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-12">
                <div class="form-check">
                  <input
                    class="form-check-input"
                    type="checkbox"
                    value=""
                    id="enable-sms"
                    v-model="formData.consent_to_the_processing_of_your_personal_data"
                    @change="changeGDPRPermissionForm"
                    required
                  />
                  <label class="form-check-label" for="enable-sms"
                    >Would you like to receive updates, promotions, and other marketing
                    communications from us via email and sms?</label
                  >
                </div>
              </div>
              <div class="col-md-12">
                <div class="form-check">
                  <input
                    class="form-check-input"
                    type="checkbox"
                    value=""
                    id="process-data"
                    v-model="formData.receive_updates_promotions_and_other"
                    @change="changeGDPRPermissionForm"
                    required
                  />
                  <label class="form-check-label" for="process-data"
                    >Do you consent to the processing of your personal data for the
                    purposes outlined in our privacy policy?</label
                  >
                </div>
              </div>
              <div class="col-md-12">
                <div class="form-check mt-5">
                  <input
                    class="form-check-input"
                    type="checkbox"
                    value=""
                    id="gdpr-terms"
                    v-model="formData.gdpr"
                    @change="changeGDPR"
                    required
                  />
                  <label class="form-check-label" for="gdpr-terms"
                    >By clicking here, I state that I have read and understood the terms
                    and conditions.</label
                  >
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Submit</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import moment from "moment";

export default {
  name: "GDPR",
  props: ["isGDPRModalOpen"],
  emits: ["closeGDPRModal", "updateGDPRFlag"],
  data() {
    return {
      formData: {
        receive_updates_promotions_and_other: null,
        consent_to_the_processing_of_your_personal_data: null,
        gdpr: null,
        gdprDatetime: null,
      },
      disabledBtn: false,
      errorMsg: null,
    };
  },
  mounted() {
    this.setGDPRLocalStorageValues();
  },
  methods: {
    async gdprSubmit(event) {
      event.preventDefault();
      this.disabledBtn = true;
      let validation = this.checkForm(event);
      if (validation) {
        this.disabledBtn = false;
        this.$emit("updateGDPRFlag", true);
        this.$ls.set("gdpr_form", this.formData);
        this.closeModal(event);
      } else {
        this.disabledBtn = false;
      }
    },
    changeGDPR(e) {
      e.preventDefault();
      this.formData.gdpr = e.target.checked ? e.target.checked : null;
      this.formData.gdprDatetime = e.target.checked
        ? moment.utc().format("DD-MM-YYYY HH:mm:ss")
        : null;
    },
    setGDPRLocalStorageValues() {
      if (this.$ls.get("gdpr_form")) {
        let gdprFormData = this.$ls.get("gdpr_form");
        this.formData.gdpr = gdprFormData.gdpr;
        this.formData.gdprDatetime = gdprFormData.gdprDatetime;
        this.formData.consent_to_the_processing_of_your_personal_data =
          gdprFormData.consent_to_the_processing_of_your_personal_data;
        this.formData.receive_updates_promotions_and_other =
          gdprFormData.receive_updates_promotions_and_other;
        this.$emit("updateGDPRFlag", true);
      }
    },
    checkForm(e) {
      if (
        this.formData.gdpr != null &&
        this.formData.gdprDatetime != null &&
        this.formData.consent_to_the_processing_of_your_personal_data != null &&
        this.formData.receive_updates_promotions_and_other != null
      )
        return true;
      let form = document.querySelector(".needs-validation-gdpr");
      form.classList.add("was-validated");
      e.preventDefault();
      return false;
    },
    closeModal(e) {
      e.preventDefault();
      this.$emit("closeGDPRModal");
    },
  },
  watch: {
    isGDPRModalOpen(newVal) {
      if (newVal) {
        this.setGDPRLocalStorageValues();
      }
    },
  },
};
</script>

<style scoped>
.modal-backdrop {
  background-color: rgba(0, 0, 0, 0.5);
  /* Adjust opacity if needed */
}
</style>
