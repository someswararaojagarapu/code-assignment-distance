<?php

namespace App\CodeAssignmentDistance\Service;

use App\CodeAssignmentDistance\Service\ApiClient;

class DistanceCalculationService
{
    public function __construct(
        private readonly ApiClient $apiClient
    ) {
    }

    public function calculateDistance()
    {
        $url = ''; /** TODO */
        $response = $this->apiClient->doGetRequest('GET', $url);

        return $response;
    }
}