<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post("/login", [\App\Http\Controllers\UserController::class, "login"]);
Route::post("/admin-login", [\App\Http\Controllers\UserController::class, "adminLogin"]);
Route::post("/logout", [\App\Http\Controllers\UserController::class, "logout"]);
Route::post("/verify-code", [\App\Http\Controllers\UserController::class, "verifyCode"]);

Route::get("/occupation-list", [\App\Http\Controllers\OccupationController::class, "index"]);
Route::get("/speech-language-diagnosis-list", [\App\Http\Controllers\SpeechLanguageDiagnosisController::class, "index"]);

// clinician apis
Route::prefix('/clinician')->group(function () {
    Route::post("/validation", [\App\Http\Controllers\ClinicianController::class, "validation"]);
    Route::post("/register", [\App\Http\Controllers\ClinicianController::class, "register"]);
    Route::post("/learner-registration-by-clinician", [\App\Http\Controllers\ClinicianController::class, "learnerRegistrationByClinician"]);
    Route::get("/", [\App\Http\Controllers\ClinicianController::class, "index"]);
    Route::put("/update-system-settings/{id}", [\App\Http\Controllers\ClinicianController::class, "updateSystemSettings"]);
    Route::get("/latest-actions/{id}", [\App\Http\Controllers\ClinicianController::class, "getLatestActions"]);
    Route::get("/{clinician_id}/homework-helper", [\App\Http\Controllers\ClinicianController::class, "getHomeworkHelper"]);
    Route::get("/{clinician_id}/assessment", [\App\Http\Controllers\ClinicianController::class, "getAssessment"]);
    Route::get("/{clinician_id}/literacy-diagnostic", [\App\Http\Controllers\ClinicianController::class, "getLiteracyDiagnostic"]);
});

// learner apis
Route::prefix('/learner')->group(function () {
    Route::post("/register", [\App\Http\Controllers\LearnerController::class, "register"]);
    Route::post("/update", [\App\Http\Controllers\LearnerController::class, "update"]);
    Route::get("/encrypted-data", [\App\Http\Controllers\LearnerEncryptDataController::class, "index"]);
    Route::get("/", [\App\Http\Controllers\LearnerController::class, "index"]);
    Route::get("/{id}", [\App\Http\Controllers\LearnerController::class, "show"]);
    Route::get("/parents/{parents_id}", [\App\Http\Controllers\LearnerController::class, "getLearnersByParentsId"]);
    Route::put("/update-homework-helper/{id}", [\App\Http\Controllers\LearnerController::class, "updateHomeworkHelper"]);
});

// parents apis
Route::prefix('/parents')->group(function () {
    Route::post("/validation", [\App\Http\Controllers\ParentController::class, "validation"]);
    Route::get("/", [\App\Http\Controllers\ParentController::class, "index"]);
    Route::post("/register", [\App\Http\Controllers\ParentController::class, "register"]);
    Route::post("/learner-registration-by-parents", [\App\Http\Controllers\ParentController::class, "learnerRegistrationByParent"]);
    Route::post("/clinician-learner-link", [\App\Http\Controllers\ParentController::class, "clinicianLearnerLink"]);
    Route::get("/latest-actions/{id}", [\App\Http\Controllers\ParentController::class, "getLatestActions"]);
    Route::get("/{parents_id}/learner/{learner_id}", [\App\Http\Controllers\ParentController::class, "getLearnerDetails"]);
    Route::post("/{parents_id}/quick-learner-create", [\App\Http\Controllers\ParentController::class, "quickLearnerCreate"]);
    Route::get("/{parents_id}/service-learners", [\App\Http\Controllers\ParentController::class, "getServiceLearners"]);
    Route::get("/{parents_id}/learner/{learner_id}/service-details", [\App\Http\Controllers\ParentController::class, "getLearnerServiceDetails"]);
    Route::get("/{parents_id}/homework-helper", [\App\Http\Controllers\ParentController::class, "getHomeworkHelper"]);
    Route::get("/{parents_id}/assessment", [\App\Http\Controllers\ParentController::class, "getAssessment"]);
    Route::get("/{parents_id}/literacy-diagnostic", [\App\Http\Controllers\ParentController::class, "getLiteracyDiagnostic"]);
});

Route::prefix('/admin')->group(function () {
    Route::get("/cards-details", [\App\Http\Controllers\AdminController::class, "index"]);
    // assessement list
    Route::prefix('/assessment')->group(function () {
        Route::get("/", [\App\Http\Controllers\AdminController::class, "assessmentsList"]);
    });
    Route::prefix('/literacy-diagnostic')->group(function () {
        Route::get("/", [\App\Http\Controllers\AdminController::class, "literacyDiagnosticsList"]);
    });
    Route::prefix('/homework-helpers')->group(function () {
        Route::get("/", [\App\Http\Controllers\AdminController::class, "homeworkHelpersList"]);
    });

    Route::prefix('/clinician')->group(function () {
        Route::get("/", [\App\Http\Controllers\ClinicianController::class, "index"]);
        Route::post("/{id}/assessment-service", [\App\Http\Controllers\AdminController::class, "assignClinicianForAssessmentService"]);
        Route::post("/{id}/literacy-service", [\App\Http\Controllers\AdminController::class, "assignClinicianForLiteracyService"]);
        Route::post("/{id}/homework-helper-service", [\App\Http\Controllers\AdminController::class, "assignClinicianForHomeworkHelperService"]);
    });

    Route::prefix('/parents')->group(function () {
        Route::get("/", [\App\Http\Controllers\ParentController::class, "index"]);
    });

    Route::prefix('/learner')->group(function () {
        Route::get("/", [\App\Http\Controllers\AdminController::class, "getLearnersByClinicianId"]);
    });

    // whats-new apis
    Route::prefix('/whats-new')->group(function () {
        Route::get("/", [\App\Http\Controllers\WhatsNewController::class, "index"]);
        Route::post("/create", [\App\Http\Controllers\WhatsNewController::class, "store"]);
        Route::get("/edit/{id}", [\App\Http\Controllers\WhatsNewController::class, "show"]);
        Route::post("/update/{id}", [\App\Http\Controllers\WhatsNewController::class, "update"]);
        Route::delete("/delete/{id}", [\App\Http\Controllers\WhatsNewController::class, "delete"]);
        Route::post("/file-upload", [\App\Http\Controllers\WhatsNewController::class, "fileUpload"]);
    });

    // category apis
    Route::prefix('/category')->group(function () {
        Route::get("/", [\App\Http\Controllers\CategoryController::class, "index"]);
        Route::post("/create", [\App\Http\Controllers\CategoryController::class, "store"]);
        Route::get("/edit/{id}", [\App\Http\Controllers\CategoryController::class, "show"]);
        Route::put("/update/{id}", [\App\Http\Controllers\CategoryController::class, "update"]);
        Route::delete("/delete/{id}", [\App\Http\Controllers\CategoryController::class, "delete"]);
    });
});

Route::post('/send-sms', [\App\Http\Controllers\ClinicianController::class, 'send']);

Route::put('/user/{id}/additional-comments', [\App\Http\Controllers\UserController::class, 'addAdditionalComments']);
Route::put('/user/{id}/update-viewed', [\App\Http\Controllers\UserController::class, 'updateViewed']);
Route::put('/user/{id}/update-viewed-assessment', [\App\Http\Controllers\UserController::class, 'updateViewedAssessment']);
Route::put('/user/{id}/update-viewed-literacy', [\App\Http\Controllers\UserController::class, 'updateViewedLiteracy']);
Route::put('/user/{id}/update-viewed-homework-helper', [\App\Http\Controllers\UserController::class, 'updateViewedHomeworkHelper']);

Route::get('/user/{id}', [\App\Http\Controllers\UserController::class, 'index']);
Route::get('/user/{id}/calendar-webhook-response', [\App\Http\Controllers\UserController::class, 'getCalendarWebhookResponse']);

// subscribe api
Route::post('/subscribe', [\App\Http\Controllers\SubscriptionController::class, 'subscribe']);
Route::post('/service-subscribe', [\App\Http\Controllers\SubscriptionController::class, 'serviceSubscribe']);

// plans api
Route::get('/plans', [\App\Http\Controllers\PlanController::class, 'index']);
Route::get('/plan-price', [\App\Http\Controllers\PlanController::class, 'planPrice']);

Route::post('/webhook/stripe', [\App\Http\Controllers\StripeWebhookController::class, 'handleWebhook']);
Route::post('/webhook/get-chattiepant-calendar-response', [\App\Http\Controllers\CalendarWebhookController::class, 'handleWebhook']);

// unity webhook
Route::prefix('/v1')->group(function () {
    Route::post("/login", [\App\Http\Controllers\UnityWebhookController::class, "login"]);
    Route::get('/user', [\App\Http\Controllers\UnityWebhookController::class, 'getUser']);
    Route::prefix('/clinician')->group(function () {
        Route::get('/{id}/get-learners', [\App\Http\Controllers\UnityWebhookController::class, 'getLearners']);
        Route::get('/{id}/learner-details/{learner_id}', [\App\Http\Controllers\UnityWebhookController::class, 'getLearnerDetails']);
        Route::post('/{id}/update-learner-details/{learner_id}', [\App\Http\Controllers\UnityWebhookController::class, 'updateLearnerDetails']);
        Route::post('/{id}/learner-session/{learner_id}', [\App\Http\Controllers\UnityWebhookController::class, 'getLearnerSession']);
    });
});
