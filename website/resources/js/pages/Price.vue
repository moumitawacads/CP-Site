<template>
  <div>
    <div class="container-fluid pricingTable pb-30">
      <div class="container">
        <div class="d-flex justify-content-center pb-30">
          <h1 style="color: #151f26">Choose best plan for your business</h1>
        </div>
        <div class="row">
          <div class="col-12">
            <div class="inner d-flex tabsBtnHolder">
              <ul>
                <li>
                  <p id="services" class="" @click="choosePlan($event)">Services</p>
                </li>
                <li>
                  <p id="individual" class="active" @click="choosePlan($event)">
                    Individuals and Professionals
                  </p>
                </li>

                <li class="indicator" style="left: 243px"></li>
              </ul>
            </div>
          </div>
          <div class="col-md-12 mt-2" v-if="choosenPlanName === 'individual'">
            <div class="align-items-center d-flex justify-content-center toggle-btn">
              <span style="margin: 0.8em">Monthly</span>
              <label class="switch">
                <input type="checkbox" :checked="checkboxChecked" id="checbox" @click="check" />
                <span :class="sliderClass" class="slider round"></span>
              </label>
              <span style="margin: 0.8em">Annually</span>
            </div>
          </div>
        </div>

        <div id="servicesPriceList" class="servicesPriceList d-none animated mt-5">
          <div class="row">
            <template v-for="(servicesPlan, indexSP) in servicesPlansList" :key="indexSP + 1">
              <div class="col-md-3">
                <div class="inner holder">
                  <div class="hdng">
                    <p>{{ servicesPlan.name }}</p>
                  </div>
                  <div class="price">
                    <template v-for="(planPrice, indexPrice) in servicesPlan.prices" :key="indexPrice + 1">
                      <p class="">
                        <b>${{ planPrice.price }}</b><span> / Annual(One-time)</span>
                      </p>
                    </template>
                  </div>
                  <div class="info">
                    <p>
                      {{ servicesPlan.description }}
                    </p>
                  </div>
                  <div class="btn">
                    <template v-for="(planPrice, indexPrice) in servicesPlan.prices" :key="indexPrice + 1">
                      <a href="#" :class="disableSelection
                        ? isPlanSelected(planPrice.id, planPrice.billing_period)
                          ? 'readon active'
                          : 'readon'
                        : userData
                          ? isPlanSelected(planPrice.id, planPrice.billing_period)
                            ? 'readon active'
                            : 'readon'
                          : 'readon'
                        " @click="
                          disableSelection
                            ? isPlanSelected(planPrice.id, planPrice.billing_period)
                              ? goToCart($event)
                              : addToCart($event, servicesPlan, planPrice)
                            : userData
                              ? isPlanSelected(planPrice.id, planPrice.billing_period)
                                ? goToCart($event)
                                : addToCart($event, servicesPlan, planPrice)
                              : openModal($event, servicesPlan, planPrice)
                          ">{{
                            disableSelection
                              ? isPlanSelected(planPrice.id, planPrice.billing_period)
                                ? "Go to Cart"
                                : "Add to cart"
                              : userData
                                ? isPlanSelected(planPrice.id, planPrice.billing_period)
                                  ? "Go to Cart"
                                  : "Add to cart"
                                : "Sign Up"
                          }}</a>
                    </template>
                  </div>
                </div>
              </div>
            </template>
          </div>
        </div>

        <div id="individualPriceList" class="individualPriceList animated">
          <div class="row">
            <template v-for="(individualPlan, indexIP) in individualPlansList" :key="indexIP + 1">
              <div class="col-md-3">
                <div class="inner holder" :class="isPlanUpgrade &&
                  exisitngPlan &&
                  exisitngPlan.name === individualPlan.name
                  ? 'disabled'
                  : ''
                  ">
                  <div class="hdng">
                    <p>{{ individualPlan.name }}</p>
                  </div>
                  <div class="price">
                    <template v-for="(
                        individualPlanPrice, indexIndividualPrice
                      ) in individualPlan.prices" :key="indexIndividualPrice + 1">
                      <p class="" v-if="
                        individualPlanPrice.billing_period === 'annual'
                          ? annualPackageLabel
                          : monthlyPackageLabel
                      ">
                        <b>${{ individualPlanPrice.price }}</b><span>
                          /
                          {{
                            individualPlanPrice.billing_period === "annual"
                              ? "Annual(One-time)"
                              : individualPlanPrice.billing_period
                          }}</span>
                      </p>
                    </template>
                  </div>
                  <div class="info individual-info">
                    <p v-if="individualPlan.name === 'Homework Helper'" v-html="individualPlan.description"></p>
                    <p v-else>
                      {{ individualPlan.description }}
                    </p>
                  </div>
                  <div class="btn">
                    <template v-for="(
                        individualPlanPrice, indexIndividualPrice
                      ) in individualPlan.prices" :key="indexIndividualPrice + 1">
                      <a href="#" v-if="
                        individualPlanPrice.billing_period === 'annual'
                          ? annualPackageLabel
                          : monthlyPackageLabel
                      " :class="disableSelection
                        ? isPlanSelected(
                          individualPlanPrice.id,
                          individualPlanPrice.billing_period
                        )
                          ? 'readon active'
                          : 'readon'
                        : userData
                          ? isPlanSelected(
                            individualPlanPrice.id,
                            individualPlanPrice.billing_period
                          )
                            ? 'readon active'
                            : 'readon'
                          : 'readon'
                        " @click="
                          disableSelection
                            ? isPlanSelected(
                              individualPlanPrice.id,
                              individualPlanPrice.billing_period
                            )
                              ? goToCart($event)
                              : addToCart($event, individualPlan, individualPlanPrice)
                            : userData
                              ? isPlanSelected(
                                individualPlanPrice.id,
                                individualPlanPrice.billing_period
                              )
                                ? goToCart($event)
                                : addToCart($event, individualPlan, individualPlanPrice)
                              : openModal($event, individualPlan, individualPlanPrice)
                          ">{{
                            disableSelection
                              ? isPlanSelected(
                                individualPlanPrice.id,
                                individualPlanPrice.billing_period
                              )
                                ? "Go to Cart"
                                : "Add to cart"
                              : userData
                                ? isPlanSelected(
                                  individualPlanPrice.id,
                                  individualPlanPrice.billing_period
                                )
                                  ? "Go to Cart"
                                  : "Add to cart"
                                : "Sign Up"
                          }}</a>
                    </template>
                  </div>
                </div>
              </div>
            </template>
          </div>

          <div class="row">
            <div class="col-md-12 mt-5 text-center">
              <h3>Plan Comparison</h3>
            </div>
            <FeatureFunctions @updateHeight="updateHeight" />
            <BasicFeature :heights="childHeights" />
            <ProfessionalFeature :heights="childHeights" />
            <ProfessionalPlusFeature :heights="childHeights" />
          </div>
        </div>
      </div>
    </div>
  </div>
  <SignUpModal :isModalOpen="isModalOpen" @close="closeModal" />
</template>

<script>
import BasicFeature from "../components/BasicFeature.vue";
import FeatureFunctions from "../components/FeatureFunctions.vue";
import ProfessionalFeature from "../components/ProfessionalFeature.vue";
import ProfessionalPlusFeature from "../components/ProfessionalPlusFeature.vue";
import SignUpModal from "../components/SignUpModal.vue";

export default {
  name: "Price",
  components: {
    FeatureFunctions,
    BasicFeature,
    ProfessionalFeature,
    ProfessionalPlusFeature,
    SignUpModal,
  },
  data() {
    return {
      activeClass: true,
      checkboxChecked: false,
      monthlyPackageLabel: true,
      annualPackageLabel: false,
      addToCartDisable: true,
      monthlyServicesPackages: true,
      childHeights: {},
      servicesPlansList: null,
      individualPlansList: null,
      selectedPlans: null,
      isModalOpen: false,
      userData: this.$ls.get("userData") || null,
      choosenPlanName: "individual",
      disableSelection: false,
    };
  },
  computed: {
    sliderClass() {
      return {
        "checked-slider": this.checkboxChecked,
      };
    },
    haveQueryParams() {
      return Object.keys(this.$route.query).length > 0;
    },
    isPlanUpgrade() {
      return (
        Object.keys(this.$route.query).length > 0 &&
        this.$route.query.plan &&
        this.$route.query.plan === "upgrade"
      );
    },
    exisitngPlan() {
      if (this.isPlanUpgrade) {
        let defaultPlans = ["Basic", "Professional", "Professional Plus"];
        return this.$store.state.providerData.user.plans.filter((plan) => {
          return defaultPlans.includes(plan.plan.name);
        })[0].plan;
      }
    },
  },
  mounted() {
    this.getPlans();
    this.selectedPlans = this.$store.state.cart;
    if (this.haveQueryParams > 0) {
      if (this.$route.query.plan && this.$route.query.plan === "upgrade") {
        this.disableSelection = true;
      } else {
        this.choosenPlanName = "sevices";
        this.disableSelection = true;
        document.getElementById("services").classList.add("active");
        document.getElementById("individual").classList.remove("active");

        document.getElementById("servicesPriceList").classList.remove("d-none");
        document.getElementById("servicesPriceList").classList.add("fadeIn");
        document.getElementById("individualPriceList").classList.add("d-none");

        document.getElementsByClassName("indicator")[0].style.left = "2px";
      }
    }
  },
  methods: {
    updateHeight(id, height) {
      this.childHeights[id] = height;
    },
    async getPlans() {
      const response = await axios.get("/api/plans");
      let plans = response.data.data;
      this.servicesPlansList = plans.filter((plan) => {
        return plan.type === "services";
      });
      this.individualPlansList = plans.filter((plan) => {
        return plan.type === "individual";
      });
    },
    choosePlan(e) {
      e.preventDefault();
      if (e.target.id === "services") {
        document.getElementById("services").classList.add("active");
        document.getElementById("individual").classList.remove("active");

        document.getElementById("servicesPriceList").classList.remove("d-none");
        document.getElementById("servicesPriceList").classList.add("fadeIn");
        document.getElementById("individualPriceList").classList.add("d-none");

        document.getElementsByClassName("indicator")[0].style.left = "2px";
      } else {
        document.getElementById("individual").classList.add("active");
        document.getElementById("services").classList.remove("active");

        document.getElementById("individualPriceList").classList.remove("d-none");
        document.getElementById("individualPriceList").classList.add("fadeIn");
        document.getElementById("servicesPriceList").classList.add("d-none");

        document.getElementsByClassName("indicator")[0].style.left = "243px";
      }
      this.choosenPlanName = e.target.id;
    },
    check(e) {
      e.preventDefault();
      this.checkboxChecked = !this.checkboxChecked;
      this.monthlyPackageLabel = !this.monthlyPackageLabel;
      this.annualPackageLabel = !this.monthlyPackageLabel;
      this.monthlyServicesPackages = !this.monthlyServicesPackages;
    },
    addToCart(e, plan, selectedPlanPrice) {
      e.preventDefault();
      const item = this.$store.state.cart.find(
        (p) => p.selectedPlanPrice.id === selectedPlanPrice.id
      );
      if (item) {
        item.quantity += 1;
      } else {
        this.$store.state.cart.push({
          ...plan,
          quantity: 1,
          learnerOptions: [
            {
              type: null,
              learnerId: "",
              learnerName: "",
              learnerAge: "",
              newLearnerID: "",
              used: false,
              assessmentSelectCount: 0,
              planName: plan.name,
            },
          ],
          selectedPlanPrice: selectedPlanPrice,
        });
      }

      this.$ls.set("cart", this.$store.state.cart);
      if (this.haveQueryParams) {
        let userData = this.$store.state.providerData;
        this.$router.push({
          path: "/shopping-cart",
          query:
            userData.user.role_slug === "parents"
              ? (this.$route.query.plan ? { parent_id: userData.id, plan: "upgrade" } : { parent_id: userData.id })
              : { clinician_id: userData.id, plan: "upgrade" },
        });
      } else {
        this.$router.push({ name: "shopping-cart" });
      }
    },
    isPlanSelected(planId, billingPeriod) {
      return this.selectedPlans.find(
        (p) =>
          p.selectedPlanPrice.id === planId &&
          p.selectedPlanPrice.billing_period === billingPeriod
      );
    },
    goToCart(e) {
      e.preventDefault();
      if (this.haveQueryParams) {
        let userData = this.$store.state.providerData;
        this.$router.push({
          path: "/shopping-cart",
          query:
            userData.user.role_slug === "parents"
              ? (this.$route.query.plan ? { parent_id: userData.id, plan: "upgrade" } : { parent_id: userData.id })
              : { clinician_id: userData.id, plan: "upgrade" },
        });
      } else {
        this.$router.push({ name: "shopping-cart" });
      }
    },
    openModal(e, plan, selectedPlanPrice) {
      e.preventDefault();
      const item = this.$store.state.cart.find(
        (p) => p.selectedPlanPrice.id === selectedPlanPrice.id
      );
      if (item) {
        item.quantity += 1;
      } else {
        this.$store.state.cart.push({
          ...plan,
          quantity: 1,
          selectedPlanPrice: selectedPlanPrice,
        });
      }

      this.$ls.set("cart", this.$store.state.cart);
      this.isModalOpen = true;
    },
    closeModal() {
      this.$ls.remove("cart");
      this.isModalOpen = false;
    },
  },
};
</script>

<style scoped>
html,
body {
  font-size: 15px;
  color: #505050;
  font-family: "Rubik", sans-serif;
  vertical-align: baseline;
  line-height: 25px;
  font-weight: 400;
  overflow-x: hidden;
}

p {
  margin: 0 0;
}

a {
  color: #0061d5;
  transition: all 0.3s ease 0s;
  text-decoration: none !important;
  outline: none !important;
}

a:active,
a:hover {
  text-decoration: none;
  outline: 0 none;
  color: #242526;
}

ul {
  list-style: outside none none;
  margin: 0;
  padding: 0;
}

::-moz-selection {
  background: #0061d5;
  text-shadow: none;
  color: #ffffff;
}

::selection {
  background: #0061d5;
  text-shadow: none;
  color: #ffffff;
}

.pt-90 {
  padding-top: 90px !important;
}

.pt-60 {
  padding-top: 60px !important;
}

.pb-30 {
  padding-bottom: 30px !important;
}

.p-15 {
  padding: 15px 0;
}

.hdng {
  min-height: 100px;
}

.price {
  min-height: 115px;
}

.info {
  min-height: 130px;
}

.individual-info {
  min-height: 190px;
}

.not-applicable {
  color: #333 !important;
}

/* ------------------------------------
02. Global CSS
---------------------------------------*/
.readon {
  position: relative;
  display: inline-block !important;
  background: #0061d5;
  padding: 14px 30px;
  line-height: normal;
  color: #ffffff !important;
  transition: all 0.3s ease 0s;
  border-radius: 30px;
  text-transform: capitalize !important;
  cursor: pointer;
  box-shadow: 0 6px 30px rgba(0, 0, 0, 0.1);
  -ms-box-shadow: 0 6px 30px rgba(0, 0, 0, 0.1);
  -webkit-box-shadow: 0 6px 30px rgba(0, 0, 0, 0.1);
  -moz-box-shadow: 0 6px 30px rgba(0, 0, 0, 0.1);
}

.readon:hover,
.readon:focus {
  background: #242526;
}

.inner {
  width: 100%;
  float: left;
  position: relative;
}

.pricingTable .holder {
  background: #fff;
  box-shadow: 1px 20px 12px -15px rgba(0, 0, 0, 0.2);
  padding: 40px 15px;
  text-align: center;
  border: 1px solid rgba(0, 0, 0, 0.05);
  transition: 0.5s ease;
}

.pricingTable .holder:hover {
  transform: translateY(-5px);
}

.pricingTable .holder-comparison:hover {
  transform: none !important;
}

.pricingTable .holder .hdng p {
  font-size: 28px;
  font-weight: bold;
  color: #242526;
}

.pricingTable .holder .img img {
  width: 70%;
}

.pricingTable .holder .price p {
  color: #0061d5;
  margin-bottom: 25px;
}

.pricingTable .holder .price p b {
  font-size: 40px;
  font-weight: bold;
}

.pricingTable .holder .price p span {
  font-size: 18px;
}

.pricingTable .holder .info p {
  margin-bottom: 15px;
  color: #242526;
  font-weight: 14px;
}

.pricingTable .holder.active {
  background: #0061d5;
}

.pricingTable .holder.active .hdng p,
.pricingTable .holder.active .price p,
.pricingTable .holder.active .info p {
  color: #fff;
}

.pricingTable .holder.active .readon {
  background: #fff;
  color: #0061d5 !important;
}

.pricingTable .holder.active .readon:hover {
  background: #242526;
  color: #fff !important;
}

.readon.active {
  background: #242526;
  color: #fff !important;
}

.pricingTable .tabsBtnHolder ul {
  float: left;
  display: block;
  width: 100%;
  max-width: 490px;
  border-radius: 1.6666666667rem;
  margin: 0px auto;
  margin-bottom: 15px;
  background: #0061d5;
  text-align: center;
  position: relative;
}

.pricingTable .tabsBtnHolder ul li {
  float: left;
  width: calc(100% / 2);
  display: inline-block;
  transition: 0.4s ease;
}

.pricingTable .tabsBtnHolder ul li p {
  color: #fff;
  padding: 10px 15px;
  z-index: 10;
  position: relative;
  cursor: pointer;
}

.pricingTable .tabsBtnHolder ul li p.active {
  color: #0061d5;
}

.pricingTable .tabsBtnHolder ul li.indicator {
  position: absolute;
  top: 50%;
  left: 2px;
  /*163px*/
  background: #fff;
  height: calc(100% - 4px);
  transform: translateY(-50%);
  border-radius: 1.5333333333rem;
  width: 245px;
  z-index: 9;
}

.toggle-btn {
  margin: 0 auto 20px;
}

.switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
}

.switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #50bfe6;
  -webkit-transition: 0.4s;
  transform: translate(0px, 0px);
  transition: 0.6s ease transform, 0.6s box-shadow;
}

.slider:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: 0.4s;
  transition: 0.4s;
}

.slider {
  transition: 0.4s;
}

.checked-slider {
  background-color: #1e2321 !important;
}

.checked-slider:before {
  transform: translateX(26px);
}

.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}

.fa-check {
  color: #26c281;
  font-size: 20px;
}

.fa-xmark {
  color: #ca1111;
  font-size: 20px;
}

.alternative {
  background: #e4e4e4;
  padding: 8px 0;
}

.p-40 {
  padding: 40px 0 0 !important;
}

.p-8 {
  padding: 8px !important;
}

.info-details {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 8px;
}

.go-to-bag {
  background-color: #242526;
}

.disabled {
  pointer-events: none;
  background-color: rgb(0, 0, 0, 0.082) !important;
}
</style>
