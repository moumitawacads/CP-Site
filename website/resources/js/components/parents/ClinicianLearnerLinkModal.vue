<template>
  <div class="modal fade" :class="{ show: showModal }" :style="showModal ? 'display: block;' : 'display: none;'"
    tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">{{ $t("Clinician Learner Link") }}</h5>
          <button type="button" class="close" @click="closeClinicianLearnerLinkModal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form class="needs-validation" novalidate ref="clinicianLearnerLinkForm" @submit.prevent="verify">
            <div class="form-group mt-2">
              <label for="password">{{ $t("clinician_name") }}:</label>
              <input v-model="formData.clinician_name" type="text" class="form-control"
                :placeholder="$t('enter_name_placeholder')" required />
              <div class="invalid-feedback">{{ $t(errors.clinician_name) }}</div>
            </div>
            <div class="form-group mt-2">
              <label for="password">{{ $t("clinician_code") }}:</label>
              <input v-model="formData.clinician_code" type="text" class="form-control" :placeholder="$t('enter_code')"
                required />
              <div class="invalid-feedback">{{ $t(errors.clinician_code) }}</div>
            </div>
            <div class="mt-3"></div>
            <span class="err" v-if="errorMsg">{{ $t(errorMsg) }}</span>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" :disabled="disabledBtn"
            @click="closeClinicianLearnerLinkModal">
            {{ $t("close") }}
          </button>
          <button type="submit" class="btn btn-primary" :disabled="disabledBtn" @click="verify">
            {{ $t("submit") }}
          </button>
        </div>
      </div>
    </div>
  </div>
  <!-- Backdrop -->
  <div v-if="showModal" class="modal-backdrop fade show" @click="closeClinicianLearnerLinkModal"></div>
</template>

<script>
import axios from "../../axios";
export default {
  name: "ClinicianLearnerLinkModal",
  props: ["showModal", "learner_id", "learner_encrypt_data_id", "parent_id"],
  emits: ["closeclinicianmodal"],
  data() {
    return {
      formData: {
        learner_id: "",
        learner_encrypt_data_id: "",
        clinician_name: "",
        clinician_code: "",
        tab_id: "my-learners",
        parent_id: "",
      },
      disabledBtn: false,
      errorMsg: null,
      errors: {
        clinician_name: "Clinician Name field is required.",
        clinician_code: "Clinician Code field is required.",
      },
    };
  },
  mounted() {
    // console.log(this.learner_id, this.learner_encrypt_data_id);
  },
  methods: {
    async verify() {
      this.disabledBtn = true;
      let validation = this.checkForm(event);
      if (validation) {
        try {
          this.errorMsg = null;
          this.formData.learner_id = this.learner_id;
          this.formData.learner_encrypt_data_id = this.learner_encrypt_data_id;
          this.formData.parent_id = this.parent_id;

          const response = await axios.post(
            "/api/parents/clinician-learner-link",
            this.formData,
            {
              headers: {
                Authorization: "Bearer " + this.$store.state.token,
              },
            }
          );
          if (response && response.status == 200) {
            this.disabledBtn = false;
            this.closeClinicianLearnerLinkModal();
          }
        } catch (error) {
          this.disabledBtn = false;
          this.errorMsg = error.response.data.message;
        }
      } else {
        this.disabledBtn = false;
      }
    },
    checkForm(e) {
      if (this.formData.clinician_name != "" && this.formData.clinician_code != "")
        return true;
      let form = document.querySelector(".needs-validation");
      form.classList.add("was-validated");
      e.preventDefault();
      return false;
    },
    closeClinicianLearnerLinkModal() {
      this.$emit("closeclinicianmodal");
    },
  },
};
</script>

<style scoped>
.modal-backdrop {
  background-color: rgba(0, 0, 0, 0.5);
  /* Adjust opacity if needed */
}

.modal.fade.show {
  opacity: 1;
  transition: opacity 0.15s linear;
}

.modal.fade {
  opacity: 0;
}

.close:not(:disabled):not(.disabled) {
  cursor: pointer;
}

.modal-header .close {
  padding: 1rem;
  margin: -1rem -1rem -1rem auto;
}

button.close {
  padding: 0;
  background-color: transparent;
  border: 0;
  -webkit-appearance: none;
}

.close {
  float: right;
  font-size: 1.5rem;
  font-weight: 700;
  line-height: 1;
  color: #000;
  text-shadow: 0 1px 0 #fff;
  opacity: 0.5;
}

.err {
  color: red;
}
</style>
