<template>
    <div>
        <h2 class="mb-4">Dashboard</h2>
        <div class="row" v-if="cardsDetails">
            <div class="col-xl-3 col-lg-3">
                <div class="card l-bg-cherry small-box text-bg-primary">
                    <div class="card-statistic-3 p-10">
                        <h3 class="card-title mb-0">{{ cardsDetails.clinicians_count }}</h3>
                        <p>Clinicians</p>
                    </div>
                    <router-link to="/admin/clinicians" class="small-box-footer">More
                        info</router-link>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3">
                <div class="card l-bg-cherry small-box text-bg-success">
                    <div class="card-statistic-3 p-10">
                        <h3 class="card-title mb-0">{{ cardsDetails.learners_count }}</h3>
                        <p>Learners</p>
                    </div>
                    <a class="small-box-footer">More info</a>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3">
                <div class="card l-bg-cherry small-box text-bg-warning">
                    <div class="card-statistic-3 p-10">
                        <h3 class="card-title mb-0">{{ cardsDetails.parents_count }}</h3>
                        <p>Parents</p>
                    </div>
                    <router-link to="/admin/parents" class="small-box-footer">More
                        info</router-link>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3">
                <div class="card l-bg-cherry small-box text-bg-secondary">
                    <div class="card-statistic-3 p-10">
                        <h3 class="card-title mb-0">{{ cardsDetails.services_count }}</h3>
                        <p>Services</p>
                    </div>
                    <router-link to="/admin/services/assessments" @click="toggleServices" class="small-box-footer">More
                        info</router-link>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "Cards",
    emits: ["toggleServices"],
    data() {
        return {
            cardsDetails: null
        }
    },
    mounted() {
        this.getAllCardsDetails();
    },
    methods: {
        async getAllCardsDetails() {
            try {
                const response = await axios.get("/api/admin/cards-details", {
                    headers: {
                        Authorization: `Bearer ${this.$store.state.token}`,
                    },
                });
                this.cardsDetails = response.data.data;
            } catch (error) {
                console.error(error);
            }
        },
        toggleServices() {
            this.$emit("toggleServices");
        }
    }
}
</script>

<style scoped>
.text-right {
    text-align: right;
}

.p-10 {
    padding: 10px;
}

.small-box {
    border-radius: .375rem;
    box-shadow: 0 0 1px rgba(var(--bs-body-color-rgb), .125), 0 1px 3px rgba(var(--bs-body-color-rgb), .2);
    position: relative;
    display: block;
    margin-bottom: 1.25rem;
    --bs-link-color-rgb: none;
    --bs-link-hover-color-rgb: none;
    --bs-heading-color: none;
    border: none;
}

.small-box-footer {
    position: relative;
    z-index: 10;
    display: block;
    padding: 3px 0;
    text-align: center;
    background-color: #00000012;
}
</style>