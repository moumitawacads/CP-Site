<template>
  <section class="mt-4">
    <p>Detailed Learner Report</p>
  </section>
</template>

<script>
import axios from "../../axios";
export default {
  emits: ["removeNewLearnerId"],
  data() {
    return {
      formData: {
        user_id: "",
        verification_code: "",
      },
      errorMsg: null,
    };
  },
  methods: {},
};
</script>
<style scoped>
h1 {
  color: #4caf50;
}

.container {
  max-width: 600px;
  border: 2px solid rgb(0, 0, 0);
  border-radius: 8px;
  padding: 20px;
}

.form-input {
  transition: border-color 0.3s ease;
  border: 1px solid #ccc;
  border-radius: 4px;
  padding: 8px;
  width: 100%;
  margin-bottom: 10px;
}

.form-input:focus {
  border-color: #4f46e5;
  outline: none;
}

.err {
  color: red;
}
</style>
