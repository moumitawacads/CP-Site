<template>
  <div class="toast-container position-fixed top-0 end-0 p-3">
    <div
      v-for="(toast, index) in toasts"
      :key="index"
      class="toast show"
      role="alert"
      aria-live="assertive"
      aria-atomic="true"
    >
      <div class="toast-header">
        <strong class="me-auto">{{ toast.title }}</strong>
        <button type="button" class="btn-close" @click="removeToast(index)"></button>
      </div>
      <div class="toast-body">{{ toast.message }}</div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      toasts: [],
    };
  },
  methods: {
    addToast(title, message) {
      this.toasts.push({ title, message });
      setTimeout(() => {
        this.toasts.shift();
      }, 10000); // Auto-hide after 10 seconds
    },
    removeToast(index) {
      this.toasts.splice(index, 1);
    },
  },
};
</script>

<style scoped>
.toast-container {
  z-index: 1055;
}
.toast-header {
  background-color: #e4e4e4;
  color: #333;
}
.toast-body {
  background-color: #f6f6f6;
  color: #333;
}
</style>
