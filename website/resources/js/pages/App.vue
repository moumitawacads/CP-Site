<template>
  <div>
    <Header :isAdminRoute="isAdminRoute" />
    <section class="bg-light base-height" :class="(!isAdminRoute || $route.name === 'admin-login') && $route.name != 'home'
        ? 'pt-120'
        : 'pt-71'
      ">
      <router-view></router-view>
    </section>
  </div>
</template>

<script>
import Header from "./Header.vue";

export default {
  name: "App",
  components: {
    Header,
  },
  computed: {
    isAdminRoute() {
      return this.$route.path.startsWith("/admin");
    },
  },
  mounted() {
    this.getSpeechLanguageDiagnosisList();
  },
  methods: {
    async getSpeechLanguageDiagnosisList() {
      this.$store.dispatch("getSpeechLanguageDiagnosisList");
    },
  },
};
</script>

<style scoped>
h1 {
  color: #4caf50;
}

a {
  text-decoration: none;
}

.base-height {
  min-height: 100vh;
}

.pt-120 {
  padding-top: 120px;
}

.pt-71 {
  padding-top: 71px;
}
</style>
