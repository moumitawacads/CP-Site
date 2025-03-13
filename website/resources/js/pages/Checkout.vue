<template>
  <div class="container">
    <div class="row mt-5">
      <div class="col-md-4 order-md-2 mb-4">
        <h4 class="d-flex justify-content-between align-items-center mb-3">
          <span class="text-muted">Your cart</span>
          <span class="badge badge-secondary badge-pill">3</span>
        </h4>
        <ul class="list-group mb-3">
          <li class="list-group-item d-flex justify-content-between lh-condensed">
            <div>
              <h6 class="my-0">Product name</h6>
              <small class="text-muted">Brief description</small>
            </div>
            <span class="text-muted">$12</span>
          </li>
          <li class="list-group-item d-flex justify-content-between lh-condensed">
            <div>
              <h6 class="my-0">Second product</h6>
              <small class="text-muted">Brief description</small>
            </div>
            <span class="text-muted">$8</span>
          </li>
          <li class="list-group-item d-flex justify-content-between lh-condensed">
            <div>
              <h6 class="my-0">Third item</h6>
              <small class="text-muted">Brief description</small>
            </div>
            <span class="text-muted">$5</span>
          </li>
          <li class="list-group-item d-flex justify-content-between bg-light">
            <div class="text-success">
              <h6 class="my-0">Promo code</h6>
              <small>EXAMPLECODE</small>
            </div>
            <span class="text-success">-$5</span>
          </li>
          <li class="list-group-item d-flex justify-content-between">
            <span>Total (USD)</span>
            <strong>$20</strong>
          </li>
        </ul>
        <hr class="mb-4" />

        <h4 class="mb-3">Payment</h4>

        <div class="d-block my-3">
          <div class="custom-control custom-radio" style="display: inline-block">
            <input
              id="credit"
              name="paymentMethod"
              type="radio"
              class="custom-control-input"
              checked
              required
            />
            <label class="custom-control-label" for="credit">Credit card</label>
          </div>
          <div
            class="custom-control custom-radio"
            style="display: inline-block; margin-left: 20px"
          >
            <input
              id="debit"
              name="paymentMethod"
              type="radio"
              class="custom-control-input"
              required
            />
            <label class="custom-control-label" for="debit">Debit card</label>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6 mb-3">
            <input
              type="text"
              class="form-control"
              id="cc-name"
              placeholder="Name on card"
              required
            />
            <!-- <small class="text-muted">Full name as displayed on card</small> -->
            <div class="invalid-feedback">Name on card is required</div>
          </div>
          <div class="col-md-6 mb-3">
            <input
              type="text"
              class="form-control"
              id="cc-number"
              placeholder="Credit card number"
              required
            />
            <div class="invalid-feedback">Credit card number is required</div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-9 mb-3">
            <input
              type="text"
              class="form-control"
              id="cc-expiration"
              placeholder="Expiration"
              required
            />
            <div class="invalid-feedback">Expiration date required</div>
          </div>
          <div class="col-md-3 mb-3">
            <input
              type="text"
              class="form-control"
              id="cc-cvv"
              placeholder="CVV"
              required
            />
            <div class="invalid-feedback">Security code required</div>
          </div>
        </div>
        <hr class="mb-4" />
        <div class="row">
          <button class="btn btn-primary btn-lg btn-block" type="submit">
            Continue to checkout
          </button>
        </div>
      </div>
      <div class="col-md-8 order-md-1">
        <h4 class="mb-3">Billing address</h4>
        <form class="needs-validation" novalidate>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="firstName">First name</label>
              <input
                type="text"
                class="form-control"
                id="firstName"
                placeholder=""
                value=""
                required
              />
              <div class="invalid-feedback">Valid first name is required.</div>
            </div>
            <div class="col-md-6 mb-3">
              <label for="lastName">Last name</label>
              <input
                type="text"
                class="form-control"
                id="lastName"
                placeholder=""
                value=""
                required
              />
              <div class="invalid-feedback">Valid last name is required.</div>
            </div>
          </div>

          <div class="mb-3">
            <label for="email">Email</label>
            <input
              type="email"
              class="form-control"
              id="email"
              placeholder="you@example.com"
            />
            <div class="invalid-feedback">
              Please enter a valid email address for shipping updates.
            </div>
          </div>

          <div class="mb-3">
            <label for="phone_number">Phone Number</label>
            <input
              type="text"
              class="form-control"
              id="phone_number"
              placeholder="9876543210"
            />
            <div class="invalid-feedback">
              Please enter a valid email address for shipping updates.
            </div>
          </div>

          <div class="mb-3">
            <label for="address">Address</label>
            <input
              type="text"
              class="form-control"
              id="address"
              placeholder="1234 Main St"
              required
            />
            <div class="invalid-feedback">Please enter your shipping address.</div>
          </div>

          <div class="mb-3">
            <label for="address2"
              >Address 2 <span class="text-muted">(Optional)</span></label
            >
            <input
              type="text"
              class="form-control"
              id="address2"
              placeholder="Apartment or suite"
            />
          </div>

          <div class="row">
            <div class="col-md-5 mb-3">
              <label for="country">Country</label>
              <select class="form-select d-block w-100" id="country" required>
                <option value="">Choose...</option>
                <option>United States</option>
              </select>
              <div class="invalid-feedback">Please select a valid country.</div>
            </div>
            <div class="col-md-4 mb-3">
              <label for="state">State</label>
              <select class="form-select d-block w-100" id="state" required>
                <option value="">Choose...</option>
                <option>California</option>
              </select>
              <div class="invalid-feedback">Please provide a valid state.</div>
            </div>
            <div class="col-md-3 mb-3">
              <label for="zip">Zip</label>
              <input type="text" class="form-control" id="zip" placeholder="" required />
              <div class="invalid-feedback">Zip code required.</div>
            </div>
          </div>
          <hr class="mb-4" />
          <h4>Usage Agreement</h4>
          <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" id="same-address" />
            <label class="custom-control-label" for="same-address"
              >Consent for Speech Language Form appears.</label
            >
          </div>
          <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" id="save-info" />
            <label class="custom-control-label" for="save-info"
              >Required to Complete Virtual Readiness Assessment</label
            >
          </div>
          <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" id="save-info" />
            <label class="custom-control-label" for="save-info"
              >Required to click GDPR.</label
            >
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
export default {};
</script>

<style scoped>
.custom-control {
  position: relative;
  display: block;
  min-height: 1.5rem;
  padding-left: 1.5rem;
}
.custom-control-input {
  position: absolute;
  z-index: -1;
  opacity: 0;
}
.custom-checkbox .custom-control-input:checked ~ .custom-control-label::before {
  background-color: #007bff;
}
.custom-control-input:focus ~ .custom-control-label::before {
  box-shadow: 0 0 0 1px #fff, 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}
.custom-control-input:checked ~ .custom-control-label::before {
  color: #fff;
  background-color: #007bff;
}
.custom-checkbox .custom-control-label::before {
  border-radius: 0.25rem;
}
.custom-control-label::before {
  position: absolute;
  top: 0.25rem;
  left: 0;
  display: block;
  width: 1rem;
  height: 1rem;
  pointer-events: none;
  content: "";
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
  background-color: #dee2e6;
}
.custom-checkbox .custom-control-input:checked + .custom-control-label::after {
  background-image: url("data:image/svg+xml;charset=utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3E%3Cpath fill='%23fff' d='M6.564.75l-3.59 3.612-1.538-1.55L0 4.26 2.974 7.25 8 2.193z'/%3E%3C/svg%3E");
}
.custom-control-label::after {
  position: absolute;
  top: 0.25rem;
  left: 0;
  display: block;
  width: 1rem;
  height: 1rem;
  content: "";
  background-repeat: no-repeat;
  background-position: center center;
  background-size: 50% 50%;
}

.custom-control-input:checked + .custom-control-label::before {
  color: #fff;
  background-color: #007bff;
}
.custom-radio .custom-control-label::before {
  border-radius: 50%;
}
.custom-radio .custom-control-input:checked + .custom-control-label::after {
  background-image: url("data:image/svg+xml;charset=utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3E%3Ccircle r='3' fill='%23fff'/%3E%3C/svg%3E");
}
.custom-radio .custom-control-input:disabled:checked + .custom-control-label::before {
  background-color: rgba(0, 123, 255, 0.5);
}
</style>
