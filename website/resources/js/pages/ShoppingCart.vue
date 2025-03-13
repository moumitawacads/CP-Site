<template>
  <div class="container">
    <form class="needs-validation" autocomplete="off">
      <div>
        <div class="row">
          <div class="col-md-12">
            <h3>Hey {{ userData ? getFullName(userData.fullname) : "" }},</h3>
          </div>
        </div>
      </div>
      <div class="card m-0 mt-3">
        <div class="row">
          <div class="col-md-8 cart">
            <div class="title">
              <div class="row">
                <div class="col">
                  <h4><b>Shopping Cart</b></h4>
                </div>
                <div class="col align-self-center text-right text-muted">
                  {{ shoppingCartCount }} {{ shoppingCartCount === 1 ? "item" : "items" }}
                </div>
              </div>
              <div class="row mt-2">
                <div class="col">Item</div>
                <div class="col-2 text-center">Quantity</div>
                <div class="col-2 text-right">Price</div>
                <div class="col-2" style="width: 11%">Action</div>
              </div>
            </div>
            <template v-for="(shoppingCart, index) in shoppingCarts" :key="index + 1">
              <div class="row border-top">
                <div class="row main align-items-center">
                  <div class="col">
                    <h5 style="margin-top: 0px">{{ shoppingCart.name }}</h5>
                    <div class="text-muted" v-if="shoppingCart.name === 'Homework Helper'"
                      v-html="shoppingCart.description"></div>
                    <div class="text-muted" v-else>
                      {{ shoppingCart.description }}
                    </div>
                    <div class="text-muted mt-2">
                      <b style="padding: 0">Billing Period:
                        {{
                          shoppingCart.selectedPlanPrice.billing_period === "monthly"
                            ? "Monthly"
                            : "Annual(One-time)"
                        }}</b>
                    </div>
                  </div>
                  <div class="col-2 text-center">
                    <input class="quantity" type="number" :value="shoppingCart.quantity" min="1"
                      @change="changeQuantity($event, shoppingCart.selectedPlanPrice.id)" />
                  </div>
                  <div class="col-2 text-right">
                    $
                    {{ shoppingCart.quantity * shoppingCart.selectedPlanPrice.price }}
                  </div>
                  <div class="col-1">
                    <span class="close"><button class="btn btn-sm btn-danger" type="button"
                        @click="removePlan(shoppingCart.selectedPlanPrice.id)">
                        <i class="fas fa-solid fa-trash"></i></button></span>
                  </div>
                </div>
                <div class="row mb-3" v-if="haveQueryParams && !isNormalPlan(shoppingCart.name)">
                  <div v-for="serviceIndex in shoppingCart.quantity"
                    :key="`service-${shoppingCart.id}-item-${serviceIndex}`" class="mt-3">
                    <div class="col-md-6">
                      <div class="card" :id="'service-' + index + '-learner-' + serviceIndex" :class="shoppingCart.learnerOptions[serviceIndex - 1].used
                        ? 'disabled'
                        : ''
                        ">
                        <div class="card-body">
                          <div class="form-check">
                            <input class="form-check-input" type="radio"
                              :name="'learnerType' + index + '-' + serviceIndex" required
                              :id="'existingLearner' + index + '-' + serviceIndex" @change="
                                chooseLearnerOption(
                                  $event,
                                  index,
                                  serviceIndex,
                                  'existing'
                                )
                                " value="existing" v-model="shoppingCart.learnerOptions[serviceIndex - 1].type" />
                            <label class="form-check-label" :for="'existingLearner' + index + '-' + serviceIndex">
                              Choose Existing Learner
                            </label>
                          </div>
                          <div class="form-check">
                            <input class="form-check-input" type="radio"
                              :name="'learnerType' + index + '-' + serviceIndex"
                              :id="'newLearner' + index + '-' + serviceIndex" required @change="
                                chooseLearnerOption($event, index, serviceIndex, 'new')
                                " value="new" v-model="shoppingCart.learnerOptions[serviceIndex - 1].type" />
                            <label class="form-check-label" :for="'newLearner' + index + '-' + serviceIndex">
                              Create a New Learner
                            </label>
                          </div>
                          <div class="invalid-feedback">Please Select a type</div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div v-if="
                        shoppingCart.learnerOptions[serviceIndex - 1].type ===
                        'existing' && !shoppingCart.learnerOptions[serviceIndex - 1].used
                      " :id="'service-' + index + '-existing-learner-' + serviceIndex" class="mt-3">
                        <!-- Existing Learner Section -->
                        <div class="card">
                          <div class="card-body">
                            <div class="form-group">
                              <label for="learnerName">Learner Name</label>
                              <select class="form-select m-0" id="learnerName" v-model="shoppingCart.learnerOptions[serviceIndex - 1].learnerId
                                " @change="checkHomeworkHelper($event, index, serviceIndex)" required>
                                <option value="">-- Select --</option>
                                <option v-for="(learnerData, learnerIndex) in learnersData" :key="learnerIndex"
                                  :value="learnerData.learner_data.id">
                                  {{ learnerData.learner_data.user.fullname }}
                                </option>
                              </select>
                              <div class="invalid-feedback">
                                Please Select a learner name
                              </div>
                            </div>
                            <button type="button" class="btn btn-primary mt-2"
                              @click="createQuickLearner(index, serviceIndex - 1)">
                              Submit
                            </button>
                          </div>
                        </div>
                      </div>

                      <div v-if="
                        shoppingCart.learnerOptions[serviceIndex - 1].type === 'new' && !shoppingCart.learnerOptions[serviceIndex - 1].used
                      " :id="'service-' + index + '-new-learner-' + serviceIndex" class="mt-3">
                        <!-- New Learner Section -->
                        <div class="card">
                          <div class="card-body">
                            <form @submit.prevent="
                              createQuickLearner(index, serviceIndex - 1)
                              ">
                              <div class="form-group">
                                <label for="newLearnerName">Learner Name</label>
                                <input type="text" class="form-control" id="newLearnerName" v-model="shoppingCart.learnerOptions[serviceIndex - 1]
                                  .learnerName
                                  " required placeholder="Enter Learner Name" />
                                <div class="invalid-feedback">
                                  Please enter a learner name
                                </div>
                              </div>
                              <div class="form-group">
                                <label for="newLearnerAge">Learner Age</label>
                                <select class="form-select m-0" v-model="shoppingCart.learnerOptions[serviceIndex - 1]
                                  .learnerAge
                                  " required>
                                  <option value="">-- Select --</option>
                                  <option v-for="age in learnerAges" :key="age.value" :value="age.value">
                                    {{ age.value }}
                                  </option>
                                </select>
                                <div class="invalid-feedback">
                                  Please Select a learner age
                                </div>
                              </div>
                              <button type="submit" class="btn btn-primary mt-2">
                                Submit
                              </button>
                            </form>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </template>
            <div class="back-to-shop d-flex justify-content-between">
              <a href="#" @click="goBack"><i class="fas fa-solid fa-arrow-left"></i><span class=""> Back to
                  shop</span></a>
              <a href="#" class="btn btn-danger" style="padding: 5px 8px; color: white" @click="clearAll"
                v-if="!haveQueryParams"><span class="">{{
                  $ls.get("userType") === "parent"
                    ? "Opps., I'm a Clinician (not a Parent)"
                    : "Opps., I'm a Parent (not a Clinician)"
                }}</span></a>
            </div>
          </div>
          <div class="col-md-4 summary">
            <div v-if="!haveQueryParams && !isPlanUpgrade">
              <h5><b>Declaration Forms</b></h5>
            </div>
            <div style="border-top: 1px solid rgba(0, 0, 0, 0.1); padding: 2vh 0"
              v-if="!haveQueryParams && !isPlanUpgrade">
              <div class="form-check p-0">
                <a class="terms-agreement" id="consent-for-speech-language"
                  @click="openConsentOfSpeechLanguageModal">Consent for
                  Speech Language Form</a>
              </div>
              <div class="form-check p-0">
                <a class="terms-agreement" id="virtual-readiness-assessment"
                  @click="openVirtualReadinessAssessmentModal">Virtual
                  Language Instruction Readiness Form</a>
              </div>
              <div class="form-check p-0">
                <a class="terms-agreement" id="gdpr" @click="openGDPRModal">GDPR Permission</a>
              </div>
            </div>
            <div>
              <h5><b>Summary</b></h5>
            </div>
            <div></div>
            <div class="row" style="border-top: 1px solid rgba(0, 0, 0, 0.1); padding: 2vh 0">
              <div class="col"><b>TOTAL PRICE</b></div>
              <div class="col text-right">
                <b>$ {{ totalPrice.toFixed(2) }}</b>
              </div>
            </div>
            <div class="form-group mt-5">
              <label for="card-element">Credit or Debit Card</label>
              <div id="card-element" class="form-control" style="padding: 15px"></div>

              <div id="card-errors" class="invalid-feedback"></div>
            </div>
            <div class="row mt-3">
              <button class="btn btn-primary" :disabled="disableBtn" @click="checkout">
                {{ loading ? "Processing" : "CHECKOUT" }}
              </button>
            </div>
            <div class="row mt-3">
              <span :class="message === 'Payment successful!'
                ? 'text-success text-center'
                : 'text-danger text-center'
                ">{{ message }}</span>
            </div>
          </div>
        </div>
      </div>
    </form>
    <ConsentOfSpeechLanguageModal :isConsentOfSpeechLanguageModalOpen="isConsentOfSpeechLanguageModalOpen"
      @closeConsentOfSpeechLanguageModal="closeConsentOfSpeechLanguageModal"
      @updateConsentOfSpeechLanguageFlag="updateConsentOfSpeechLanguageFlag" />
    <VirtualReadinessAssessment :isVirtualReadinessAssessmentModalOpen="isVirtualReadinessAssessmentModalOpen"
      @closeVirtualReadinessAssessmentModal="closeVirtualReadinessAssessmentModal"
      @updateVirtualReadinessAssessmentFlag="updateVirtualReadinessAssessmentFlag" />
    <GDPR :isGDPRModalOpen="isGDPRModalOpen" @closeGDPRModal="closeGDPRModal" @updateGDPRFlag="updateGDPRFlag" />
    <AdditionalCommentsModal :isAdditionalCommentsModalOpen="isAdditionalCommentsModalOpen"
      @closeAdditionalCommentsModal="closeAdditionalCommentsModal" />
  </div>
</template>

<script>
import { loadStripe } from "@stripe/stripe-js";
import ConsentOfSpeechLanguageModal from "../components/ConsentOfSpeechLanguageModal.vue";
import VirtualReadinessAssessment from "../components/VirtualReadinessAssessment.vue";
import GDPR from "../components/GDPR.vue";
import moment from "moment";
import AdditionalCommentsModal from "../components/AdditionalCommentsModal.vue";

export default {
  name: "ShoppingCart",
  components: {
    ConsentOfSpeechLanguageModal,
    VirtualReadinessAssessment,
    GDPR,
    AdditionalCommentsModal,
  },
  data() {
    return {
      shoppingCarts: null,
      shoppingCartCount: null,
      totalPrice: 0,
      stripe: null,
      elements: null,
      cardElement: null,
      loading: false,
      error: null,
      userData: this.$ls.get("userData") || null,
      disableBtn: false,
      message: "",
      consentOfSpeechLanguage: null,
      virtualReadinessAssessment: null,
      gdpr: null,
      isConsentOfSpeechLanguageModalOpen: false,
      isVirtualReadinessAssessmentModalOpen: false,
      isGDPRModalOpen: false,
      isAdditionalCommentsModalOpen: false,
      services: [],
      numServices: 2,
      learnersData: null,
      validationErrors: {},
      servicesPlansList: null,
    };
  },
  computed: {
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
    learnerAges() {
      return this.$store.state.learnerAges;
    },
  },
  mounted() {
    this.getPlans();
    if (this.haveQueryParams) {
      this.shoppingCarts = this.$store.state.cart;
      this.shoppingCartCount = Object.keys(this.shoppingCarts).length;
      this.calculateTotalPrice();
      this.initStripe();
      this.userData = this.$store.state.user;
      this.getLearnersByParentsId();
    } else {
      if (this.$ls.get("userData") && this.$ls.get("cart")) {
        this.shoppingCarts = this.$store.state.cart;
        this.shoppingCartCount = Object.keys(this.shoppingCarts).length;
        this.calculateTotalPrice();
        this.initStripe();
      } else {
        let responseData = this.$ls.get("responseData");
        if (responseData.user.additional_comments) {
          this.$router.push({ name: "home" });
        } else {
          this.openAdditionalCommentsModal();
        }
      }
    }
  },
  methods: {
    async initStripe() {
      try {
        this.stripe = await loadStripe(import.meta.env.VITE_STRIPE_KEY);
        this.elements = this.stripe.elements();
        this.cardElement = this.elements.create("card", {
          hidePostalCode: true, // Hide postal code if you don't need it
          style: {
            base: {
              color: "#495057", // Text color
              fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
              fontSmoothing: "antialiased",
              fontSize: "16px",
              "::placeholder": { color: "#aab7c4" }, // Placeholder color
            },
            invalid: {
              color: "#dc3545", // Bootstrap danger color
              iconColor: "#dc3545",
            },
          },
        });
        this.cardElement.mount("#card-element");
      } catch (err) {
        this.error = "Failed to initialize Stripe. Please try again.";
        console.log(this.error);
      }
    },
    async getLearnersByParentsId() {
      try {
        const response = await axios.get(
          "/api/learner/parents/" + this.$store.state.providerData.id + "/?page=all",
          {
            headers: {
              Authorization: "Bearer " + this.$store.state.token,
            },
          }
        );
        if (response && response.status == 200) {
          this.learnersData = response.data.data;
          // console.log(this.learnersData);
        }
      } catch (error) {
        console.error("Error uploading file:", error);
      }
    },
    async checkout(e) {
      e.preventDefault();
      this.disableBtn = true;
      this.loading = true;
      this.error = null;
      let validation = this.checkForm(e);

      if (validation) {
        try {
          // Create the payment method using Stripe.js
          const {
            paymentMethod,
            error: stripeError,
          } = await this.stripe.createPaymentMethod({
            type: "card",
            card: this.cardElement,
          });

          if (stripeError) {
            throw new Error(stripeError.message);
          }

          if (this.haveQueryParams) {
            let cart = this.$ls.get("cart");
            let userData = this.$store.state.providerData;
            let formData = {
              provider_id: userData.id,
              payment_method: paymentMethod.id,
              cart: JSON.stringify(cart),
              user_type: userData.user.role_slug,
              plan_type: this.$route.query.plan ? this.$route.query.plan : null,
            };
            const response = await axios.post("/api/service-subscribe", formData);
            if (response && response.status === 200) {
              this.goToNextPage(response.data);
            }
          } else {
            let userType = this.$ls.get("userType");
            let userData = this.userData;
            let cart = this.$ls.get("cart");

            if (userType === "clinician") {
              const clinicianFormData = this.getClinicianFormData(
                userData,
                paymentMethod,
                userType,
                cart
              );
              const response = await axios.post("/api/subscribe", clinicianFormData, {
                headers: {
                  "Content-Type": "multipart/form-data",
                },
              });
              if (response && response.status === 200) {
                this.goToNextPage(response.data);
              }
            } else {
              const parentFormData = this.getParentFormData(
                userData,
                paymentMethod,
                userType,
                cart
              );
              const response = await axios.post("/api/subscribe", parentFormData);
              if (response && response.status === 200) {
                this.goToNextPage(response.data);
              }
            }
          }
        } catch (err) {
          this.error = err.response.data.error || "An error occurred. Please try again.";
          this.message = this.error;
          this.disableBtn = false;
          this.loading = false;
          console.log(err.response.data.error);
        }
      } else {
        this.message = "You must agree before checkout.";
        this.disableBtn = false;
        this.loading = false;
      }
    },
    calculateTotalPrice() {
      for (let x = 0; x < this.shoppingCartCount; x++) {
        this.totalPrice +=
          this.shoppingCarts[x].quantity *
          Number(this.shoppingCarts[x].selectedPlanPrice.price);
      }
    },
    removePlan(planId) {
      let index = this.shoppingCarts
        .map((x) => {
          return x.selectedPlanPrice.id;
        })
        .indexOf(planId);
      this.shoppingCarts.splice(index, 1);
      this.$store.state.cart = this.shoppingCarts;
      this.$ls.set("cart", this.shoppingCarts);
      this.shoppingCartCount = Object.keys(this.shoppingCarts).length;
      this.totalPrice = 0;
      this.calculateTotalPrice();
    },
    changeQuantity(e, planId) {
      e.preventDefault();
      const newQuantity = parseInt(event.target.value, 10);
      if (newQuantity < 0 || isNaN(newQuantity)) {
        event.target.value = 1;
        return;
      }
      const planIndex = this.shoppingCarts.findIndex(
        (item) => item.selectedPlanPrice.id === planId
      );
      if (planIndex !== -1) {
        this.shoppingCarts[planIndex].quantity = newQuantity;
        this.getLearnerOptions(planIndex, newQuantity);

        if (this.haveQueryParams && newQuantity > 0) {
          const miniArticPlanIndex = this.shoppingCarts.findIndex(
            (item) => item.name === "Mini Artic Assessment"
          );
          const homeworkHelperPlanIndex = this.shoppingCarts.findIndex(
            (item) => item.name === "Homework Helper"
          );
          if (miniArticPlanIndex !== -1 && homeworkHelperPlanIndex !== -1) {
            if (this.shoppingCarts[miniArticPlanIndex].quantity > 1) {
              // Decrease quantity by 1
              const newQuantityMini = (this.shoppingCarts[
                miniArticPlanIndex
              ].quantity -= 1);
              this.getLearnerOptions(miniArticPlanIndex, newQuantityMini);
            }
          }
        }

        this.$store.state.cart = this.shoppingCarts;
        this.$ls.set("cart", this.shoppingCarts);
        this.shoppingCartCount = Object.keys(this.shoppingCarts).length;
        this.totalPrice = 0;
        this.calculateTotalPrice();
      }
    },
    getLearnerOptions(serviceIndex, newQuantity) {
      const currentOptions = this.shoppingCarts[serviceIndex].learnerOptions;

      if (newQuantity > currentOptions.length) {
        // Add new empty learnerOptions for increased quantity
        for (let i = currentOptions.length; i < newQuantity; i++) {
          currentOptions.push({
            type: null,
            learnerId: "",
            learnerName: "",
            learnerAge: "",
            newLearnerID: "",
            used: false,
            assessmentSelectCount: 0,
            planName: this.shoppingCarts[serviceIndex].name,
          });
        }
      } else if (newQuantity < currentOptions.length) {
        // Remove excess learnerOptions for decreased quantity
        this.shoppingCarts[serviceIndex].learnerOptions = currentOptions.slice(
          0,
          newQuantity
        );
      }
    },
    getFullName(name) {
      let words = name.split(" ");
      if (words.length > 1) {
        return words[0] + " " + words[1].substring(0, 1);
      } else {
        return words[0];
      }
    },
    updateConsentOfSpeechLanguageFlag(value) {
      this.consentOfSpeechLanguage = true;
      document
        .getElementById("consent-for-speech-language")
        .classList.remove("invalid-form");
    },
    updateVirtualReadinessAssessmentFlag(value) {
      this.virtualReadinessAssessment = true;
      document
        .getElementById("virtual-readiness-assessment")
        .classList.remove("invalid-form");
    },
    updateGDPRFlag(value) {
      this.gdpr = true;
      document.getElementById("gdpr").classList.remove("invalid-form");
    },
    openConsentOfSpeechLanguageModal() {
      this.isConsentOfSpeechLanguageModalOpen = true;
    },
    closeConsentOfSpeechLanguageModal() {
      this.isConsentOfSpeechLanguageModalOpen = false;
    },
    openVirtualReadinessAssessmentModal() {
      this.isVirtualReadinessAssessmentModalOpen = true;
    },
    closeVirtualReadinessAssessmentModal() {
      this.isVirtualReadinessAssessmentModalOpen = false;
    },
    openGDPRModal() {
      this.isGDPRModalOpen = true;
    },
    closeGDPRModal() {
      this.isGDPRModalOpen = false;
    },
    openAdditionalCommentsModal() {
      this.isAdditionalCommentsModalOpen = true;
    },
    closeAdditionalCommentsModal() {
      this.isAdditionalCommentsModalOpen = false;
    },
    checkForm(e) {
      if (
        this.consentOfSpeechLanguage != null &&
        this.virtualReadinessAssessment != null &&
        this.gdpr != null
      )
        return true;
      let form = document.querySelector(".needs-validation");
      form.classList.add("was-validated");
      if (!this.haveQueryParams && this.consentOfSpeechLanguage === null) {
        document
          .getElementById("consent-for-speech-language")
          .classList.add("invalid-form");
      }
      if (!this.haveQueryParams && this.virtualReadinessAssessment === null) {
        document
          .getElementById("virtual-readiness-assessment")
          .classList.add("invalid-form");
      }
      if (!this.haveQueryParams && this.gdpr === null) {
        document.getElementById("gdpr").classList.add("invalid-form");
      }

      if (this.haveQueryParams && !this.isPlanUpgrade) {
        return this.validateForm();
      }
      if (this.isPlanUpgrade) return true;
      e.preventDefault();
      return false;
    },
    validateForm() {
      this.validationErrors = []; // Reset all validation errors

      this.shoppingCarts.forEach((cart, serviceIndex) => {
        if (!this.validationErrors[serviceIndex]) {
          this.validationErrors[serviceIndex] = []; // Initialize the errors array for the service
        }

        cart.learnerOptions.forEach((option, optionIndex) => {
          const errors = {};

          // Validate the type
          if (!option.type) {
            errors.type = "Please select a type.";
          }

          // Validation for existing learner
          if (option.type === "existing" && !option.learnerId) {
            errors.learnerId = "Please select a learner.";
          }

          // Validation for new learner
          if (option.type === "new") {
            if (!option.learnerName) errors.learnerName = "Learner name is required.";
            if (!option.learnerAge) errors.learnerAge = "Learner age is required.";
          }

          if (!option.used) {
            errors.used = "Not submitted";
          }

          // Set validation errors for this option
          this.validationErrors[serviceIndex][optionIndex] = errors;
        });
      });

      // Check if there are any errors
      return this.validationErrors.every((serviceErrors) =>
        serviceErrors.every((optionErrors) => Object.keys(optionErrors).length === 0)
      );
    },
    getClinicianFormData(userData, paymentMethod, userType, cart) {
      const fileData = this.$ls.get("upload_clinician_certificate");
      const base64File = fileData.data; // Base64 data
      const originalFileName = fileData.name; // Original file name
      const blob = this.base64ToBlob(base64File);
      const formData = new FormData();
      formData.append("fullname", userData.fullname);
      formData.append("address_1", userData.address_1);
      formData.append("address_2", userData.address_2);
      formData.append("email", userData.email);
      formData.append("phone_number", userData.phone_number);
      formData.append("password", userData.password);
      formData.append("city", userData.city);
      formData.append("province", userData.province);
      formData.append("postal_code", userData.postal_code);
      formData.append("country", userData.country);
      formData.append("occupation_id", userData.occupation_id);
      formData.append("upload_clinician_certificate", blob);
      formData.append("preferred_language", userData.preferred_language);
      formData.append("payment_method", paymentMethod.id);
      formData.append("user_type", userType);
      formData.append("cart", JSON.stringify(cart));
      formData.append(
        "consent_of_speech_language_form",
        JSON.stringify(this.$ls.get("consent_of_speech_language_form"))
      );
      formData.append(
        "virtual_language_instruction_form",
        JSON.stringify(this.$ls.get("virtual_language_instruction_form"))
      );
      formData.append("gdpr_form", JSON.stringify(this.$ls.get("gdpr_form")));

      return formData;
    },
    getParentFormData(userData, paymentMethod, userType, cart) {
      userData.payment_method = paymentMethod.id;
      userData.user_type = userType;
      userData.cart = JSON.stringify(cart);
      userData.consent_of_speech_language_form = JSON.stringify(
        this.$ls.get("consent_of_speech_language_form")
      );
      userData.virtual_language_instruction_form = JSON.stringify(
        this.$ls.get("virtual_language_instruction_form")
      );
      userData.gdpr_form = JSON.stringify(this.$ls.get("gdpr_form"));

      return userData;
    },
    // Utility function to convert base64 to Blob
    base64ToBlob(base64Data) {
      const byteString = atob(base64Data.split(",")[1]);
      const mimeString = base64Data.split(",")[0].split(":")[1].split(";")[0];
      const byteArray = new Uint8Array(byteString.length);
      for (let i = 0; i < byteString.length; i++) {
        byteArray[i] = byteString.charCodeAt(i);
      }
      return new Blob([byteArray], { type: mimeString });
    },
    goToNextPage(responseData) {
      this.$ls.set("responseData", responseData);
      // if (!this.haveQueryParams) {
      this.$store.state.cart = [];
      this.$ls.remove("cart");
      // }
      this.$ls.remove("userData");
      this.$ls.remove("userType");
      this.$ls.remove("upload_clinician_certificate");
      this.$ls.remove("consent_of_speech_language_form");
      this.$ls.remove("virtual_language_instruction_form");
      this.$ls.remove("gdpr_form");
      this.message = "Payment successful!";
      this.disableBtn = false;
      this.loading = false;
      setTimeout(() => {
        if (this.haveQueryParams) {
          let userData = this.$store.state.providerData;
          if (this.$route.query.plan && this.$route.query.plan === "upgrade") {
            this.$router.push({ name: "dashboard" });
          } else {
            this.$router.push({
              path: "/choose-learner",
              query:
                userData.user.role_slug === "parents"
                  ? (this.$route.query.plan ? { parent_id: userData.id, plan: "upgrade" } : { parent_id: userData.id })
                  : { clinician_id: userData.id, plan: "upgrade" },
            });
          }
        } else {
          this.openAdditionalCommentsModal();
        }
      }, 2000);
    },

    goBack(e) {
      e.preventDefault();
      if (this.haveQueryParams) {
        let userData = this.$store.state.providerData;
        this.$router.push({
          path: "/price",
          query:
            userData.user.role_slug === "parents"
              ? (this.$route.query.plan ? { parent_id: userData.id, plan: "upgrade" } : { parent_id: userData.id })
              : { clinician_id: userData.id, plan: "upgrade" },
        });
      } else {
        this.$router.push("/price");
      }
    },
    chooseLearnerOption(e, index, serviceIndex, type) {
      const learnerOption = this.shoppingCarts[index].learnerOptions[serviceIndex - 1];
      learnerOption.type = type;
      learnerOption.learnerId = "";
      learnerOption.learnerName = "";
      learnerOption.learnerAge = "";
      learnerOption.newLearnerID = "";
      learnerOption.used = false;
      learnerOption.planName = this.shoppingCarts[index].name;

      if (this.shoppingCarts[index].name === "Homework Helper") {
        this.checkHomeworkHelper(e, index, serviceIndex);
      } else {
        learnerOption.assessmentSelectCount = 0;
      }
    },
    async checkHomeworkHelper(e, index, serviceIndex) {
      e.preventDefault();
      // checking plan is homework helper or not
      const learnerOption = this.shoppingCarts[index].learnerOptions[serviceIndex - 1];
      if (this.shoppingCarts[index].name === "Homework Helper") {
        if (learnerOption.type === "existing" && learnerOption.learnerId) {
          // Check assessment status
          this.getLearnerServiceDetails(learnerOption.learnerId).then(
            (assessmentDone) => {
              if (!assessmentDone) {
                // Add only one "Mini Artic Assessment" plan
                const miniArticPlanIndex = this.shoppingCarts.findIndex(
                  (item) => item.name === "Mini Artic Assessment"
                );

                if (
                  miniArticPlanIndex === -1 ||
                  learnerOption.assessmentSelectCount === 0
                ) {
                  this.addToCart();
                  learnerOption.assessmentSelectCount++;
                }
              } else {
                if (learnerOption.assessmentSelectCount != 0) {
                  this.handleDuplicateCartEntry(index, serviceIndex);
                  learnerOption.assessmentSelectCount = 0;
                }
              }
            }
          );
        } else if (
          learnerOption.type === "existing" &&
          !learnerOption.learnerId &&
          learnerOption.assessmentSelectCount != 0
        ) {
          this.handleDuplicateCartEntry(index, serviceIndex);
          learnerOption.assessmentSelectCount = 0;
        } else if (
          learnerOption.type === "new" &&
          learnerOption.assessmentSelectCount === 0
        ) {
          this.addToCart();
          learnerOption.assessmentSelectCount++;
        }
      }
    },
    async getLearnerServiceDetails(learnerId) {
      try {
        const response = await axios.get(
          "/api/parents/" +
          this.$store.state.providerData.id +
          "/learner/" +
          learnerId +
          "/service-details",
          {
            headers: {
              Authorization: "Bearer " + this.$store.state.token,
            },
          }
        );
        if (response && response.status == 200) {
          return response.data.assessment;
        }
      } catch (error) {
        console.error("Error uploading file:", error);
        return false;
      }
    },
    handleDuplicateCartEntry(index, serviceIndex) {
      const cartItem = this.shoppingCarts[index];
      const learnerOption = this.shoppingCarts[index].learnerOptions[serviceIndex - 1];
      // Find existing "Mini Artic Assessment" plan
      const miniArticPlanIndex = this.shoppingCarts.findIndex(
        (item) => item.name === "Mini Artic Assessment"
      );
      if (miniArticPlanIndex !== -1) {
        if (this.shoppingCarts[miniArticPlanIndex].quantity > 1) {
          // Decrease quantity by 1
          const newQuantity = (this.shoppingCarts[miniArticPlanIndex].quantity -= 1);
          this.getLearnerOptions(miniArticPlanIndex, newQuantity);
        } else {
          this.removePlan(this.shoppingCarts[miniArticPlanIndex].id);
        }
      }

      // Update cart details
      this.$store.state.cart = this.shoppingCarts;
      this.$ls.set("cart", this.shoppingCarts);
      this.totalPrice = 0;
      this.calculateTotalPrice();
    },
    async addToCart() {
      const miniArticPlan = this.servicesPlansList.find(
        (plan) => plan.name === "Mini Artic Assessment"
      );

      if (miniArticPlan) {
        const planPrice = miniArticPlan.prices.find((p) => p.billing_period === "annual");
        const existingPlanIndex = this.shoppingCarts.findIndex(
          (item) => item.name === "Mini Artic Assessment"
        );
        if (existingPlanIndex !== -1) {
          // Increase quantity if the plan already exists
          const newQuantity = (this.shoppingCarts[existingPlanIndex].quantity += 1);
          this.getLearnerOptions(existingPlanIndex, newQuantity);
        } else {
          // Add new entry
          this.shoppingCarts.push({
            ...miniArticPlan,
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
                planName: miniArticPlan.name,
              },
            ],
            selectedPlanPrice: planPrice,
          });
        }

        // Update cart details
        this.$store.state.cart = this.shoppingCarts;
        this.$ls.set("cart", this.shoppingCarts);
        this.shoppingCartCount = Object.keys(this.shoppingCarts).length;
        this.totalPrice = 0;
        this.calculateTotalPrice();
      }
    },
    async getPlans() {
      const response = await axios.get("/api/plans");
      let plans = response.data.data;
      this.servicesPlansList = plans.filter((plan) => {
        return plan.type === "services";
      });
    },
    async createQuickLearner(index, serviceIndex) {
      try {
        const response = await axios.post(
          "/api/parents/" + this.$store.state.providerData.id + "/quick-learner-create",
          this.shoppingCarts[index].learnerOptions[serviceIndex],
          {
            headers: {
              Authorization: "Bearer " + this.$store.state.token,
            },
          }
        );
        if (response && response.status == 200) {
          this.getLearnersByParentsId();
          this.shoppingCarts[index].learnerOptions[serviceIndex].newLearnerID = response.data.learner.id;
          this.shoppingCarts[index].learnerOptions[serviceIndex].used = true;
          this.$store.state.cart = this.shoppingCarts;
          this.$ls.set("cart", this.shoppingCarts);

          document
            .getElementById(`service-${index}-learner-${serviceIndex + 1}`)
            .classList.add("disabled");
          if (
            this.shoppingCarts[index].learnerOptions[serviceIndex].type === "existing"
          ) {
            document.getElementById(
              `service-${index}-existing-learner-${serviceIndex + 1}`
            ).style.display = "none";
          } else {
            document.getElementById(
              `service-${index}-new-learner-${serviceIndex + 1}`
            ).style.display = "none";
          }
        }
      } catch (error) {
        console.error("Error uploading file:", error);
      }
    },
    clearAll() {
      this.$store.state.cart = [];
      this.$ls.remove("cart");
      this.$ls.remove("userData");
      this.$ls.remove("userType");
      this.$ls.remove("upload_clinician_certificate");
      this.$ls.remove("responseData");
      this.$ls.remove("consent_of_speech_language_form");
      this.$ls.remove("virtual_language_instruction_form");
      this.$ls.remove("gdpr_form");
      this.$router.push("/");
    },
    isNormalPlan(planName) {
      let planNames = ["Basic", "Professional", "Professional Plus"];
      return planNames.includes(planName);
    },
  },
};
</script>

<style scoped>
.title {
  margin-bottom: 3vh;
}

.card {
  margin: auto;
  width: 100%;
  box-shadow: 0 6px 20px 0 rgba(0, 0, 0, 0.19);
  border-radius: 1rem;
  border: transparent;
}

@media (max-width: 767px) {
  .card {
    margin: 3vh auto;
  }
}

.cart {
  background-color: #fff;
  padding: 4vh 5vh;
  border-bottom-left-radius: 1rem;
  border-top-left-radius: 1rem;
}

@media (max-width: 767px) {
  .cart {
    padding: 4vh;
    border-bottom-left-radius: unset;
    border-top-right-radius: 1rem;
  }
}

.summary {
  background-color: #ddd;
  border-top-right-radius: 1rem;
  border-bottom-right-radius: 1rem;
  padding: 4vh;
  color: rgb(65, 65, 65);
}

@media (max-width: 767px) {
  .summary {
    border-top-right-radius: unset;
    border-bottom-left-radius: 1rem;
  }
}

.summary .col-2 {
  padding: 0;
}

.summary .col-10 {
  padding: 0;
}

.row {
  margin: 0;
}

.title b {
  font-size: 1.5rem;
}

.main {
  margin: 0;
  padding: 2vh 0;
  width: 100%;
}

.col-2,
.col {
  padding: 0 1vh;
}

a {
  padding: 0 1vh;
}

.close {
  margin-left: auto;
  font-size: 0.7rem;
}

img {
  width: 3.5rem;
}

.back-to-shop {
  margin-top: 4.5rem;
}

h5 {
  margin-top: 4vh;
}

hr {
  margin-top: 1.25rem;
}

form {
  padding: 2vh 0;
}

select {
  border: 1px solid rgba(0, 0, 0, 0.137);
  padding: 1.5vh 1vh;
  margin-bottom: 4vh;
  outline: none;
  width: 100%;
  background-color: rgb(247, 247, 247);
}

.quantity {
  border: 1px solid rgba(0, 0, 0, 0.137);
  padding: 1vh;
  margin-bottom: 4vh;
  outline: none;
  width: 100%;
  background-color: rgb(247, 247, 247);
}

.quantity:focus::-webkit-input-placeholder {
  color: transparent;
}

/* .btn {
  background-color: #000;
  border-color: #000;
  color: white;
  width: 100%;
  font-size: 0.7rem;
  margin-top: 4vh;
  padding: 1vh;
  border-radius: 0;
}
.btn:focus {
  box-shadow: none;
  outline: none;
  box-shadow: none;
  color: white;
  -webkit-box-shadow: none;
  -webkit-user-select: none;
  transition: none;
}
.btn:hover {
  color: white;
} */
a {
  color: black;
}

a:hover {
  color: black;
  text-decoration: none;
}

#code {
  background-image: linear-gradient(to left,
      rgba(255, 255, 255, 0.253),
      rgba(255, 255, 255, 0.185)),
    url("https://img.icons8.com/small/16/000000/long-arrow-right.png");
  background-repeat: no-repeat;
  background-position-x: 95%;
  background-position-y: center;
}

.text-right {
  text-align: right;
}

.terms-agreement:hover {
  color: #007bff;
  cursor: pointer;
}

.terms-agreement {
  padding: 0;
  color: #007bff;
}

.invalid-form {
  color: #dc3545;
}

.disabled {
  pointer-events: none;
  background-color: rgb(0, 0, 0, 0.082);
}
</style>
