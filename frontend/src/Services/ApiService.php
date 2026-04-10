<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class ApiService
{
    private Client $client;
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = $_ENV['API_BASE_URL'];
        
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => (int)$_ENV['API_TIMEOUT'],
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ]
        ]);
    }

    /**
     * Make a GET request to the API
     */
    public function get(string $endpoint, array $params = []): array
    {
        try {
            $response = $this->client->get($endpoint, [
                'query' => $params,
                'headers' => $this->getAuthHeaders()
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            return $this->handleError($e);
        }
    }

    /**
     * Make a POST request to the API
     */
    public function post(string $endpoint, array $data = []): array
    {
        try {
            $response = $this->client->post($endpoint, [
                'json' => $data,
                'headers' => $this->getAuthHeaders()
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            return $this->handleError($e);
        }
    }

    /**
     * Make a PUT request to the API
     */
    public function put(string $endpoint, array $data = []): array
    {
        try {
            $response = $this->client->put($endpoint, [
                'json' => $data,
                'headers' => $this->getAuthHeaders()
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            return $this->handleError($e);
        }
    }

    /**
     * Make a DELETE request to the API
     */
    public function delete(string $endpoint): array
    {
        try {
            $response = $this->client->delete($endpoint, [
                'headers' => $this->getAuthHeaders()
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            return $this->handleError($e);
        }
    }

    /**
     * Get authentication headers
     */
    private function getAuthHeaders(): array
    {
        $headers = [];
        
        if (isset($_SESSION['api_token'])) {
            $headers['Authorization'] = 'Bearer ' . $_SESSION['api_token'];
        }

        return $headers;
    }

    /**
     * Handle API errors
     */
    private function handleError(GuzzleException $e): array
    {
        $statusCode = $e->hasResponse() ? $e->getResponse()->getStatusCode() : 500;
        $message = $e->getMessage();

        if ($e->hasResponse()) {
            $body = json_decode($e->getResponse()->getBody()->getContents(), true);
            $message = $body['message'] ?? $message;
        }

        return [
            'success' => false,
            'error' => $message,
            'status_code' => $statusCode
        ];
    }
}
