<template>
  <div v-if="isModalOpen" class="modal-backdrop" @click="closeModal"></div>
  <div v-if="isModalOpen" class="modal d-block" tabindex="-1" style="z-index: 1050">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Sign Up</h5>
          <button type="button" class="btn-close" @click="closeModal"></button>
        </div>
        <div class="modal-body">
          <div class="container text-center">
            <div class="row justify-content-center">
              <div class="col-md-6 mb-3">
                <div class="card">
                  <div class="card-body">
                    <img
                      src="../../img/clinician.png"
                      alt="Clinician"
                      class="img-fluid mb-3"
                      style="max-height: 100px"
                    />
                    <button
                      class="btn btn-success w-100"
                      @click="signUpWith($event, 'clinician')"
                    >
                      I'm a Clinician
                    </button>
                  </div>
                </div>
              </div>
              <div class="col-md-6 mb-3">
                <div class="card">
                  <div class="card-body">
                    <img
                      src="../../img/parents.png"
                      alt="Parent"
                      class="img-fluid mb-3"
                      style="max-height: 100px"
                    />
                    <button
                      class="btn btn-primary w-100"
                      @click="signUpWith($event, 'parent')"
                    >
                      I'm a Parent
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "SignUpModal",
  props: ["isModalOpen"],
  emits: ["close"],
  methods: {
    signUpWith(e, value) {
      e.preventDefault();
      this.$router.push({ name: value + "-register" });
    },
    closeModal(e) {
      e.preventDefault();

      this.$emit("close");
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
