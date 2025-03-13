<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class HighLevelService
{
    protected $client;
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = env('HIGHLEVEL_API_KEY');
        $this->baseUrl = env('HIGHLEVEL_BASE_URL');
    }

    public function sendSms($phoneNumber, $message)
    {
        // try {
        $response = $this->client->post("{$this->baseUrl}/conversations/messages", [
            'headers' => [
                'Authorization' => "Bearer {$this->apiKey}",
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'to' => $phoneNumber,
                'body' => $message,
            ],
        ]);

        return json_decode($response->getBody(), true);
        // } catch (\Exception $e) {
        //     Log::error('Error sending SMS: ' . $e->getMessage());
        //     return ['error' => 'Failed to send SMS'];
        // }
    }
}
