<template>
  <div v-if="isAdditionalCommentsModalOpen" class="modal-backdrop" @click="closeModal"></div>
  <div v-if="isAdditionalCommentsModalOpen" class="modal d-block" tabindex="-1" style="z-index: 1050">
    <div class="modal-dialog">
      <div class="modal-content">
        <form class="needs-validation-additional" novalidate ref="additionalCommentsForm"
          @submit.prevent="additionalCommentsSubmit">
          <div class="modal-header">
            <h5 class="modal-title">Additional Comments</h5>
            <!-- <button type="button" class="btn-close" @click="closeModal"></button> -->
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-12">
                <label class="form-label">Are there any other details you would like to share that relate to your
                  appointment?</label>
                <textarea class="form-control" style="min-height: 250px" v-model="formData.additional_comments"
                  @input="checkWordLimit" required></textarea>
                <p class="d-flex justify-content-end" style="font-size: 12px; color: #a2a2a2">
                  {{ wordCount }} / 100
                </p>
                <div class="invalid-feedback">{{ $t(errors.additional_comments) }}</div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" :disabled="disabledBtn" class="btn btn-secondary" @click="skipBtn">
              Skip
            </button>
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
export default {
  name: "AdditionalCommentsModal",
  props: ["isAdditionalCommentsModalOpen"],
  emits: ["closeAdditionalCommentsModal"],
  data() {
    return {
      formData: {
        additional_comments: "",
      },
      disabledBtn: false,
      errorMsg: null,
      errors: {
        additional_comments: "Field is required.",
      },
      responseData: null,
    };
  },
  computed: {
    wordCount() {
      return this.formData.additional_comments
        .trim()
        .split(/\s+/)
        .filter((word) => word !== "").length;
    },
    haveQueryParams() {
      return Object.keys(this.$route.query).length > 0;
    },
  },
  mounted() {
    // this.responseData = this.$ls.get("responseData");
    // console.log(this.responseData);
  },
  methods: {
    checkWordLimit(event) {
      const words = this.formData.additional_comments.trim().split(/\s+/);
      if (words.length > 100) {
        // Prevent additional input beyond 100 words
        this.formData.additional_comments = words.slice(0, 100).join(" ");
      }
    },
    async additionalCommentsSubmit(event) {
      event.preventDefault();
      this.disabledBtn = true;
      try {
        let responseData = this.$ls.get("responseData");
        let userId = responseData.user.id;
        const response = await axios.put(
          "/api/user/" + userId + "/additional-comments",
          this.formData
        );
        if (response && response.status == 200) {
          this.disabledBtn = false;
          this.errorMsg = null;
          this.successMsg = response.data.message;
          if (this.haveQueryParams) {
            this.$router.push({
              path: "/calendar",
              query: {
                parent_id: this.$route.query.parent_id,
                learner_id: this.$route.query.learner_id,
              },
            });
          } else {
            this.setToken(responseData);
            this.$router.push({ name: "dashboard" });
          }
        }
      } catch (error) {
        this.errorMsg = error.response.data.message;
        this.disabledBtn = false;
        console.error("Failed", error);
      }
    },
    checkForm(e) {
      if (this.formData.additional_comments != "") return true;
      let form = document.querySelector(".needs-validation-additional");
      form.classList.add("was-validated");
      e.preventDefault();
      return false;
    },
    skipBtn(e) {
      e.preventDefault();
      if (this.haveQueryParams) {
        this.$router.push({
          path: "/calendar",
          query: {
            parent_id: this.$route.query.parent_id,
            learner_id: this.$route.query.learner_id,
          },
        });
      } else {
        let responseData = this.$ls.get("responseData");
        this.setToken(responseData);
        this.$router.push({ name: "dashboard" });
      }
    },
    closeModal(e) {
      e.preventDefault();
      this.$emit("closeAdditionalCommentsModal");
    },
    setToken(responseData) {
      this.$store.dispatch("login", responseData);
      this.$i18n.locale = responseData.user.preferred_language;
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
