<template>
  <div class="container">
    <iframe
      src="https://link.wacads.com/widget/booking/pucRUCLKsvc59hkdksKs"
      style="width: 100%; border: none; overflow: hidden; min-height: 714px"
      scrolling="no"
      id="pucRUCLKsvc59hkdksKs_1734705919745"
    ></iframe>
  </div>
</template>

<script>
export default {
  name: "Calendar",
  mounted() {
    this.$ls.get("responseData")
      ? window.addEventListener("message", this.handleIframeMessage)
      : this.$router.push({ name: "home" });
  },
  methods: {
    handleIframeMessage(event) {
      if (event.origin !== "https://link.wacads.com") {
        console.error("Message from untrusted origin:", event.origin);
        return;
      }

      // console.log("Message from iframe:", event.data);
      // Handle data sent from the iframe here
      // if (
      //   Object.keys(event.data).length > 1 &&
      //   event.data[0] === "msgsndr-booking-complete"
      // ) {
      //   let responseData = this.$ls.get("responseData");
      //   this.setToken(responseData);
      //   setTimeout(() => {
      //     this.$router.push({ name: "dashboard" }); // Adjust route as per your setup
      //   }, 4000);
      // }
    },
  },
  beforeDestroy() {
    window.removeEventListener("message", this.handleIframeMessage);
  },
};
</script>

<style></style>
