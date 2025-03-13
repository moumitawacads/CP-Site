<template>
    <div class="row g-0">
        <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
            <div class="p-2">
                <b>{{ $t("first_name") }}</b>
            </div>
        </div>
        <div class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
            <div class="p-2">{{ learnerDetails.learner_data.first_name }}</div>
        </div>
        <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
            <div class="p-2">
                <b>{{ $t("last_name") }}</b>
            </div>
        </div>
        <div class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
            <div class="p-2">{{ learnerDetails.learner_data.last_name }}</div>
        </div>
        <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
            <div class="p-2">
                <b>Plan Type</b>
            </div>
        </div>
        <div class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
            <div class="p-2">
                {{ getPlanDefaultName(learnerDetails.learner_data) }}
            </div>
        </div>
        <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
            <div class="p-2">
                <b>{{ $t("email") }}</b>
            </div>
        </div>
        <div class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
            <div class="p-2">{{ learnerDetails.learner_data.user.email }}</div>
        </div>
        <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
            <div class="p-2">
                <b>{{ $t("phone_number") }}</b>
            </div>
        </div>
        <div class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
            <div class="p-2">
                {{ learnerDetails.learner_data.user.phone_number }}
            </div>
        </div>
        <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
            <div class="p-2">
                <b>{{ $t("learner_age") }}</b>
            </div>
        </div>
        <div class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
            <div class="p-2">{{ learnerDetails.learner_data.learner_age }}</div>
        </div>
        <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
            <div class="p-2">
                <b>{{ $t("name_of_school") }}</b>
            </div>
        </div>
        <div class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
            <div class="p-2">
                {{ learnerDetails.learner_encrypt_data.name_of_school }}
            </div>
        </div>
        <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
            <div class="p-2">
                <b>{{ $t("gender") }}</b>
            </div>
        </div>
        <div class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
            <div class="p-2">{{ learnerDetails.learner_data.gender }}</div>
        </div>
        <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
            <div class="p-2">
                <b>{{ $t("grade") }}</b>
            </div>
        </div>
        <div class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
            <div class="p-2">{{ learnerDetails.learner_data.grade }}</div>
        </div>
        <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
            <div class="p-2">
                <b>{{ $t("family_diagnosis") }}</b>
            </div>
        </div>
        <div class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
            <div class="p-2">
                {{ learnerDetails.learner_encrypt_data.family_diagnosis }}
            </div>
        </div>
        <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
            <div class="p-2">
                <b>{{ $t("culture") }}</b>
            </div>
        </div>
        <div class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
            <div class="p-2">
                {{ learnerDetails.learner_encrypt_data.culture }}
            </div>
        </div>
        <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
            <div class="p-2">
                <b>{{ $t("history") }}</b>
            </div>
        </div>
        <div class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
            <div class="p-2">
                {{ learnerDetails.learner_encrypt_data.history }}
            </div>
        </div>
        <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
            <div class="p-2">
                <b>{{ $t("speech_language_diagnosis") }}</b>
            </div>
        </div>
        <div class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
            <div class="p-2">
                {{
                    getDiagnosisValue(learnerDetails.learner_encrypt_data.speech_language_diagnosis)
                }}
            </div>
        </div>
        <div class="col-5 col-md-3 bg-light border-bottom border-white border-3">
            <div class="p-2">
                <b>{{ $t("learner_type") }}</b>
            </div>
        </div>
        <div class="col-7 col-md-9 bg-light border-start border-bottom border-white border-3">
            <div class="p-2">
                {{ learnerDetails.learner_encrypt_data.learner_type }}
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "LearnerProfile",
    props: ["learnerDetails"],
    emits: ["getDiagnosisValue"],
    methods: {
        getDiagnosisValue(value) {
            this.$emit("getDiagnosisValue", value);
        },
        getClinicianNames(clinicians_data) {
            let clinicianNames = "";
            clinicians_data.forEach((clinician, index) => {
                clinicianNames += clinician.clinician_data.user.fullname + ((index + 1) < clinicians_data.length ? ", " : "");
            });
            return clinicianNames;
        },
        getPlanDefaultName(learnerData) {
            let defaultPlans = ["Basic", "Professional", "Professional Plus"];
            let plan = learnerData.parent_data ? learnerData.parent_data.user.plans.filter(plan => {
                return defaultPlans.includes(plan.plan.name);
            }) : [];
            return plan.length > 0 ? plan[0].plan.name : "";
        }
    },
};
</script>

<style></style>
