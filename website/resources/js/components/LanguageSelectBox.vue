<template>
  <div class="">
    <select class="form-select" :disabled="isTokenValid ? true : false" v-model="$store.state.selectedLanguage"
      @change="changeLanguage">
      <option value="en">EN</option>
      <option value="fr">FR</option>
    </select>
  </div>
</template>

<script>
export default {
  name: "LanguageSelectBox",
  props: ["isTokenValid"],
  data() {
    return {
      disabledSelectBox: false,
    };
  },
  mounted() {
    // Set language from localStorage if available
    const lang = this.$ls.get("lang") || "en";
    this.$i18n.locale = lang;
    this.$store.state.selectedLanguage = lang;
  },
  methods: {
    changeLanguage() {
      this.$i18n.locale = this.$store.state.selectedLanguage;
      // Send the selected language to the API
      this.$ls.set("lang", this.$store.state.selectedLanguage);
    },
  },
};
</script>

<style></style>
