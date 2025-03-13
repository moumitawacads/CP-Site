<template>
  <div>
    <div class="cp-bg text-center">
      <div class="container pt-120">
        <h1>Welcome To ChattiePant</h1>
        <!-- <hr class="divider" /> -->
        <!-- <p></p> -->
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "Home",
};
</script>

<style scoped>
.cp-bg {
  background: linear-gradient(to bottom, rgb(75 69 69 / 68%) 0%, rgb(92 68 68 / 59%) 100%),
    url("../../img/home-bg.jpg");
  width: 100%;
  height: 100vh;
  color: white;
  background-repeat: no-repeat;
  background-size: cover;
  background-position: center top;
  background-attachment: scroll;
}

h1 {
  font-size: 4.5rem;
  font-weight: 700;
}

.divider {
  height: 0.2rem;
  max-width: 3.25rem;
  margin: 1.5rem auto;
  background-color: #f4623a;
  opacity: 1;
  border-top: none;
}

.pt-120 {
  padding-top: 120px;
}
</style>
