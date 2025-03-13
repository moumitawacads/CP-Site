<template>
  <div
    v-if="isConsentOfSpeechLanguageModalOpen"
    class="modal-backdrop"
    @click="closeModal"
  ></div>
  <div
    v-if="isConsentOfSpeechLanguageModalOpen"
    class="modal d-block"
    tabindex="-1"
    style="z-index: 1050"
  >
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form
          class="needs-validation-consent"
          novalidate
          ref="consentOfSpeechLanguageForm"
          @submit.prevent="consentOfSpeechLanguageSubmit"
        >
          <div class="modal-header">
            <h5 class="modal-title">Consent Of Speech Language</h5>
            <button type="button" class="btn-close" @click="closeModal"></button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-12 mb-3">
                <div class="form-check mt-5">
                  <input
                    class="form-check-input"
                    type="checkbox"
                    value=""
                    id="consent-of-speech-language"
                    v-model="formData.consentOfSpeechLanguage"
                    @change="changeConsentOfSpeechLanguage"
                    required
                  />
                  <label class="form-check-label" for="consent-of-speech-language"
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
  name: "ConsentOfSpeechLanguageModal",
  props: ["isConsentOfSpeechLanguageModalOpen"],
  emits: ["closeConsentOfSpeechLanguageModal", "updateConsentOfSpeechLanguageFlag"],
  data() {
    return {
      formData: {
        consentOfSpeechLanguage: null,
        consentOfSpeechLanguageDatetime: null,
      },
    };
  },
  mounted() {
    this.setCOSLLocalStorageValues();
  },
  methods: {
    async consentOfSpeechLanguageSubmit(event) {
      event.preventDefault();
      this.disabledBtn = true;
      let validation = this.checkForm(event);
      if (validation) {
        this.disabledBtn = false;
        this.$emit("updateConsentOfSpeechLanguageFlag", true);
        this.$ls.set("consent_of_speech_language_form", this.formData);
        this.closeModal(event);
      } else {
        this.disabledBtn = false;
      }
    },
    setCOSLLocalStorageValues() {
      if (this.$ls.get("consent_of_speech_language_form")) {
        let consentOfSpeechLanguageFormData = this.$ls.get(
          "consent_of_speech_language_form"
        );
        this.formData.consentOfSpeechLanguage =
          consentOfSpeechLanguageFormData.consentOfSpeechLanguage;
        this.formData.consentOfSpeechLanguageDatetime =
          consentOfSpeechLanguageFormData.consentOfSpeechLanguageDatetime;
        this.$emit("updateConsentOfSpeechLanguageFlag", true);
      }
    },
    changeConsentOfSpeechLanguage(e) {
      e.preventDefault();
      this.formData.consentOfSpeechLanguage = e.target.checked ? e.target.checked : null;
      this.formData.consentOfSpeechLanguageDatetime = e.target.checked
        ? moment.utc().format("DD-MM-YYYY HH:mm:ss")
        : null;
    },
    checkForm(e) {
      if (
        this.formData.consentOfSpeechLanguage != null &&
        this.formData.consentOfSpeechLanguageDatetime != null
      )
        return true;
      let form = document.querySelector(".needs-validation-consent");
      form.classList.add("was-validated");
      e.preventDefault();
      return false;
    },
    closeModal(e) {
      e.preventDefault();
      this.$emit("closeConsentOfSpeechLanguageModal");
    },
  },
  watch: {
    isConsentOfSpeechLanguageModalOpen(newVal) {
      if (newVal) {
        this.setCOSLLocalStorageValues();
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
