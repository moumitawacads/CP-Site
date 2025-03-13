<template>
  <div class="">
    <h2 class="mb-4">{{ this.$route.params.id ? "Edit" : "Add" }} Category</h2>
    <div class="alert alert-success" role="alert" v-if="message">
      <span>{{ message }}</span>
    </div>
    <form class="needs-validation" novalidate ref="categoryForm"
      @submit.prevent="this.$route.params.id ? update($event, this.$route.params.id) : submit($event)">
      <div class="form-group">
        <label for="name">Name:</label>
        <input type="text" v-model="formData.name" class="form-control" id="name" placeholder="Enter Name" required />
        <div class="invalid-feedback">{{ errors.name }}</div>
      </div>
      <div class="form-group mt-3">
        <button type="submit" class="btn btn-primary" :disabled="disabledBtn">
          Submit
        </button>
        <router-link to="/admin/category" class="btn btn-secondary" :disabled="disabledBtn"
          style="margin-left: 8px">Back</router-link>
      </div>
    </form>
  </div>
</template>

<script>
export default {
  name: "CategoryForm",
  data() {
    return {
      formData: {
        name: "",
      },
      message: null,
      disabledBtn: false,
      errorMsg: null,
      successMsg: null,
      errors: {
        name: "Name field is required.",
      },
    };
  },
  mounted() {
    if (this.$route.params.id) {
      this.getCategory(this.$route.params.id);
    }
  },
  methods: {
    async submit(e) {
      this.disabledBtn = true;
      let validation = this.checkForm(e);
      if (validation) {
        try {
          const response = await axios.post("/api/admin/category/create", this.formData, {
            headers: {
              Authorization: `Bearer ${this.$store.state.token}`,
            },
          });
          this.message = response.data.message;
          this.disabledBtn = false;
          this.errorMsg = null;
          this.clearForm();
        } catch (error) {
          this.disabledBtn = false;
          this.errorMsg = error.response.data.message;
          console.error(error);
        }
      } else {
        this.disabledBtn = false;
      }
    },
    async update(e, id) {
      this.disabledBtn = true;
      let validation = this.checkForm(e);
      if (validation) {
        try {
          const response = await axios.put(`/api/admin/category/update/${id}`, this.formData, {
            headers: {
              Authorization: `Bearer ${this.$store.state.token}`,
            },
          });
          this.message = response.data.message;
          this.disabledBtn = false;
          this.errorMsg = null;
          this.clearForm();
        } catch (error) {
          this.disabledBtn = false;
          this.errorMsg = error.response.data.message;
          console.error(error);
        }
      } else {
        this.disabledBtn = false;
      }
    },
    async getCategory(id) {
      try {
        const response = await axios.get(`/api/admin/category/edit/${id}`, {
          headers: {
            Authorization: `Bearer ${this.$store.state.token}`,
          },
        });
        this.formData = response.data.data;
      } catch (error) {
        console.error(error);
      }
    },
    checkForm(e) {
      if (this.formData.name != "") return true;
      let form = document.querySelector(".needs-validation");
      form.classList.add("was-validated");
      e.preventDefault();
      return false;
    },
    clearForm() {
      this.formData = {
        name: "",
      };
      setTimeout(() => {
        this.$router.push("/admin/category");
      }, 2000);
    },
  },
};
</script>

<style scoped></style>
