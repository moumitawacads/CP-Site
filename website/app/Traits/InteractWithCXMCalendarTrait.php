<?php

namespace App\Traits;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

trait InteractWithCXMCalendarTrait
{
    protected $bearer = 'Bearer ';
    protected $baseUrl = "https://rest.gohighlevel.com/";

    protected $count = 0;

    /**
     * Get all custom fields from the CXM.
     *
     * @param array $payload 
     * @return bool True if the driver was successfully saved, false otherwise
     */
    public function getAllPipelines()
    {
        // Get CXM token 
        $accessToken = config('app.cxm_cp_access_token');
        $client = new Client();

        $cxmBaseApiUrl = $this->baseUrl;
        $cxmApiUrl = $cxmBaseApiUrl . 'v1/pipelines/';

        try {
            $cxmResponse = $client->get($cxmApiUrl, [
                'headers' => [
                    'Authorization' => $this->bearer . $accessToken,
                ],
            ]);

            $responseData = json_decode($cxmResponse->getBody(), true);
            Log::info('get pipelines response =>' . json_encode($responseData));
            if ($cxmResponse->getStatusCode() == 200) {
                return $responseData;
            }
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get all custom fields from the CXM.
     *
     * @param array $payload 
     * @return bool True if the driver was successfully saved, false otherwise
     */
    public function getAllCustomFields()
    {
        // Get CXM token 
        $accessToken = config('app.cxm_cp_access_token');
        $client = new Client();

        $cxmBaseApiUrl = $this->baseUrl;
        $cxmApiUrl = $cxmBaseApiUrl . 'v1/custom-fields/';

        try {
            $cxmResponse = $client->get($cxmApiUrl, [
                'headers' => [
                    'Authorization' => $this->bearer . $accessToken,
                ],
            ]);

            $responseData = json_decode($cxmResponse->getBody(), true);
            Log::info('get custom fields response =>' . json_encode($responseData));
            if ($cxmResponse->getStatusCode() == 200) {
                return $responseData;
            }
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Create contact to the CXM.
     *
     * @param array $payload 
     * @return bool True if the driver was successfully saved, false otherwise
     */
    public function createContactsWithTag($payload, $additionalInfo = null)
    {
        // Get CXM token 
        $accessToken = config('app.cxm_cp_access_token');
        $client = new Client();

        $payload = $this->setupCustomFieldsValue($payload, $additionalInfo);

        $cxmBaseApiUrl = $this->baseUrl;
        $cxmApiUrl = $cxmBaseApiUrl . 'v1/contacts/';

        try {
            $cxmResponse = $client->post($cxmApiUrl, [
                'headers' => [
                    'Authorization' => $this->bearer . $accessToken,
                    'Content-Type'  => 'application/json',
                ],
                'json' => $payload,
            ]);

            $responseData = json_decode($cxmResponse->getBody(), true);
            Log::info('contacts create response =>' . json_encode($responseData));
            if ($cxmResponse->getStatusCode() == 200) {
                return $responseData;
            }
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Create opportunity to the CXM.
     *
     * @param array $payload 
     * @param array $additionalInfo default null
     * @return bool True if the driver was successfully saved, false otherwise
     */
    public function createOpportunity($payload, $additionalInfo = null)
    {
        // Get CXM token 
        $accessToken = config('app.cxm_cp_access_token');
        $client = new Client();

        $pipelines = $this->setupPipeline($payload, $additionalInfo);

        $payload = $pipelines["payload"];

        $cxmBaseApiUrl = $this->baseUrl;
        $cxmApiUrl = $cxmBaseApiUrl . 'v1/pipelines/' . $pipelines["pipelineId"] . '/opportunities/';

        try {
            $cxmResponse = $client->post($cxmApiUrl, [
                'headers' => [
                    'Authorization' => $this->bearer . $accessToken,
                    'Content-Type'  => 'application/json',
                ],
                'json' => $payload,
            ]);

            $responseData = json_decode($cxmResponse->getBody(), true);
            Log::info('opportunity create response =>' . json_encode($responseData));
            if ($cxmResponse->getStatusCode() == 200) {
                return $responseData;
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage()); //return [];
        }
    }

    /**
     * Update opportunity to the CXM.
     *
     * @param array $payload 
     * @param array $additionalInfo default null
     * @return bool True if the driver was successfully saved, false otherwise
     */
    public function updateOpportunity($payload, $additionalInfo = null)
    {
        // Get CXM token 
        $accessToken = config('app.cxm_cp_access_token');
        $client = new Client();

        $pipelines = $this->setupPipeline($payload, $additionalInfo);

        $payload = $pipelines["payload"];

        $cxmBaseApiUrl = $this->baseUrl;
        $cxmApiUrl = $cxmBaseApiUrl . 'v1/pipelines/' . $pipelines["pipelineId"] . '/opportunities/' . $additionalInfo['opportunity_id'] . '/status';

        try {
            $cxmResponse = $client->put($cxmApiUrl, [
                'headers' => [
                    'Authorization' => $this->bearer . $accessToken,
                    'Content-Type'  => 'application/json',
                ],
                'json' => $payload,
            ]);

            $responseData = json_decode($cxmResponse->getBody(), true);
            Log::info('opportunity update response =>' . json_encode($responseData));
            if ($cxmResponse->getStatusCode() == 200) {
                return $responseData;
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage()); //return [];
        }
    }

    /**
     * Remove Appointment from the CXM.
     *
     * @param array $appointmentId the appointment id
     * @return bool True if the driver was successfully saved, false otherwise
     */
    public function removeAppointmentToCXM($appointmentId)
    {
        // Get CXM token 
        $accessToken = config('app.cxm_cp_access_token');
        Log::info('CXM Access Token=>' . $accessToken);
        $client = new Client();

        $cxmBaseApiUrl = $this->baseUrl;
        $cxmApiUrl = $cxmBaseApiUrl . 'v1/appointments/' . $appointmentId;

        try {
            $cxmResponse = $client->delete($cxmApiUrl, [
                'headers' => [
                    'Authorization' => $this->bearer . $accessToken
                ]
            ]);

            if ($cxmResponse->getStatusCode() == 200) {
                return true;
            }
            return false;
        } catch (\Exception $e) {
            throw new \Exception('Could not remove appointment in CXM.');
        }
    }

    private function setupCustomFieldsValue($payload, $additionalInfo)
    {
        $_arr = [];
        $customFields = $this->getAllCustomFields();
        $type = $payload['tags'][0];

        if (isset($customFields['customFields'])) {
            foreach ($customFields['customFields'] as $customField) {
                if ($type === 'clinician') {
                    switch ($customField['name']) {
                        case "Province":
                            $_arr['customFieldProvinceId'] = $customField['id'];
                            break;
                        case "Occupation":
                            $_arr['customFieldOccupationId'] = $customField['id'];
                            break;
                        case "Preferred Language":
                            $_arr['customFieldPreferredLanguageId'] = $customField['id'];
                            break;
                    }
                } else if ($type === 'parents') {
                    switch ($customField['name']) {
                        case "Why did you sign up for Chattiepant?":
                            $_arr['customFieldReasonId'] = $customField['id'];
                            break;
                        case "Preferred Language":
                            $_arr['customFieldPreferredLanguageId'] = $customField['id'];
                            break;
                    }
                } else if ($type === 'learner') {
                    switch ($customField['name']) {
                        case "Created By":
                            $_arr['customFieldCreatedById'] = $customField['id'];
                            break;
                        case "Creater Type":
                            $_arr['customFieldCreatorTypeId'] = $customField['id'];
                            break;
                        case "Gender":
                            $_arr['customFieldGenderId'] = $customField['id'];
                            break;
                        case "Learner Age":
                            $_arr['customFieldLearnerAgeId'] = $customField['id'];
                            break;
                        case "Name of School/Clinic":
                            $_arr['customFieldNameOfSchoolId'] = $customField['id'];
                            break;
                        case "Grade":
                            $_arr['customFieldGradeId'] = $customField['id'];
                            break;
                        case "Culture":
                            $_arr['customFieldCultureId'] = $customField['id'];
                            break;
                        case "Family Diagnosis":
                            $_arr['customFieldFamilyDiagnosisId'] = $customField['id'];
                            break;
                        case "History":
                            $_arr['customFieldHistoryId'] = $customField['id'];
                            break;
                        case "Learner Type":
                            $_arr['customFieldLearnerTypeId'] = $customField['id'];
                            break;
                        case "Speech Language Diagnosis":
                            $_arr['customFieldSpeechLanguageDiagnosisId'] = $customField['id'];
                            break;
                        case "Preferred Language":
                            $_arr['customFieldPreferredLanguageId'] = $customField['id'];
                            break;
                    }
                }
            }
        }
        if (count($_arr) > 0) {
            if ($type === 'clinician') {
                $payload['customField'] = [
                    $_arr['customFieldProvinceId'] => $additionalInfo['province'],
                    $_arr['customFieldOccupationId'] => $additionalInfo['occupation'],
                    $_arr['customFieldPreferredLanguageId'] => $additionalInfo['preferred_language'],
                ];
            } else if ($type === 'parents') {
                $payload['customField'] = [
                    $_arr['customFieldReasonId'] => $additionalInfo['reason'],
                    $_arr['customFieldPreferredLanguageId'] => $additionalInfo['preferred_language'],
                ];
            } else if ($type === 'learner') {
                $payload['customField'] = [
                    $_arr['customFieldCreatedById'] => isset($additionalInfo['clinician_name']) ? $additionalInfo['clinician_name'] : "",
                    $_arr['customFieldCreatorTypeId'] => isset($additionalInfo['creator_type']) ? $additionalInfo['creator_type'] : "",
                    $_arr['customFieldGenderId'] => isset($additionalInfo['gender']) ? $additionalInfo['gender'] : "",
                    $_arr['customFieldLearnerAgeId'] => isset($additionalInfo['learner_age']) ? $additionalInfo['learner_age'] : "",
                    $_arr['customFieldNameOfSchoolId'] => isset($additionalInfo['name_of_school']) ? $additionalInfo['name_of_school'] : "",
                    $_arr['customFieldGradeId'] => isset($additionalInfo['grade']) ? $additionalInfo['grade'] : "",
                    $_arr['customFieldCultureId'] => isset($additionalInfo['culture']) ? $additionalInfo['culture'] : "",
                    $_arr['customFieldFamilyDiagnosisId'] => isset($additionalInfo['family_diagnosis']) ? $additionalInfo['family_diagnosis'] : "",
                    $_arr['customFieldHistoryId'] => isset($additionalInfo['history']) ? $additionalInfo['history'] : "",
                    $_arr['customFieldLearnerTypeId'] => isset($additionalInfo['learner_type']) ? $additionalInfo['learner_type'] : "",
                    $_arr['customFieldSpeechLanguageDiagnosisId'] => isset($additionalInfo['speech_language_diagnosis']) ? $additionalInfo['speech_language_diagnosis'] : "",
                    $_arr['customFieldPreferredLanguageId'] => isset($additionalInfo['preferred_language']) ? $additionalInfo['preferred_language'] : "",
                ];
            }
        }

        return $payload;
    }

    private function setupPipeline($payload, $additionalInfo)
    {
        $defaultPlans = ["Basic", "Professional", "Professional Plus"];
        if (in_array($additionalInfo["plan_name"], $defaultPlans)) {
            $pipelineName = "Default Service";
        } else {
            $pipelineName = $additionalInfo["plan_name"] . " Service";
        }

        $pipelineId = "";
        $stageId = "";
        $pipelines = $this->getAllPipelines();
        if (isset($pipelines['pipelines'])) {
            foreach ($pipelines['pipelines'] as $pipeline) {
                if ($pipeline['name'] === $pipelineName) {
                    $pipelineId = $pipeline['id'];
                    if (isset($pipeline['stages'])) {
                        foreach ($pipeline['stages'] as $stage) {
                            if ($stage['name'] === 'Payment Completed') {
                                $stageId = $stage['id'];
                            }
                        }
                    }
                } else if ($pipeline['name'] === $pipelineName) {
                    $pipelineId = $pipeline['id'];
                    if (isset($pipeline['stages'])) {
                        foreach ($pipeline['stages'] as $stage) {
                            if ($stage['name'] === 'Payment Completed') {
                                $stageId = $stage['id'];
                            }
                        }
                    }
                } else if ($pipeline['name'] === $pipelineName) {
                    $pipelineId = $pipeline['id'];
                    if (isset($pipeline['stages'])) {
                        foreach ($pipeline['stages'] as $stage) {
                            if ($stage['name'] === 'Payment Completed') {
                                $stageId = $stage['id'];
                            }
                        }
                    }
                } else if ($pipeline['name'] === $pipelineName) {
                    $pipelineId = $pipeline['id'];
                    if (isset($pipeline['stages'])) {
                        foreach ($pipeline['stages'] as $stage) {
                            if ($stage['name'] === 'Payment Completed') {
                                $stageId = $stage['id'];
                            }
                        }
                    }
                }
            }
        }

        if ($stageId != "") {
            $payload['stageId'] = $stageId;
        }

        return [
            "pipelineId" => $pipelineId,
            "payload" => $payload
        ];
    }
}
