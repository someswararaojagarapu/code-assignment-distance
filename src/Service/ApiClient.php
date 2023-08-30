<?php

declare(strict_types=1);

namespace App\CodeAssignmentDistance\Service;

use App\CodeAssignmentDistance\Exception\CustomApiException;
use Symfony\Component\HttpClient\HttpClient;

class ApiClient
{
    private mixed $client;

    public function __construct()
    {
        $this->client = HttpClient::create();
    }

    public function doGetRequest(
        string $method,
        string $url
    ): array {
        // Send GET request
        try {
            $response = $this->client->request(
                $method,
                $url
            );
            // Get the response content as a string
            $content = $response->getContent(false);
            // Decode the JSON response
            $responseData = json_decode($content, true);

            return [
                'statusCode' => $response->getStatusCode(),
                'body' => $responseData,
            ];
        } catch (\Exception $e) {
            throw new CustomApiException($e->getMessage(), $e->getCode());
        }
    }
}
