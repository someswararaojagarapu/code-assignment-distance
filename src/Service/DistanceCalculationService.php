<?php

declare(strict_types=1);

namespace App\CodeAssignmentDistance\Service;

use App\CodeAssignmentDistance\Service\ApiClient;

class DistanceCalculationService
{
    public function __construct(
        public readonly array $listOfAddresses,
        public readonly string $googleApiHost,
        private readonly ApiClient $apiClient
    ) {
    }

    public function calculateDistance()
    {
        $response = $this->apiClient->doGetRequest('GET', $this->googleApiHost);

        return $response;
    }
}