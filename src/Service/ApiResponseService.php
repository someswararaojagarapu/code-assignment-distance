<?php

declare(strict_types=1);

namespace App\CodeAssignmentDistance\Service;

use Symfony\Component\HttpFoundation\JsonResponse;

class ApiResponseService
{
    public function __construct(
        private readonly ApiClient $apiClient
    ) {
    }

    public function fetchAPIResponse(string $url): array | JsonResponse
    {
        try {
            return $this->apiClient->doGetRequest('GET', $url);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'An error occurred while fetching data.'], 500);
        }
    }
}