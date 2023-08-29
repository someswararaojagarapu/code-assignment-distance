<?php

declare(strict_types=1);

namespace App\CodeAssignmentDistance\Tests\Unit;

use App\CodeAssignmentDistance\Service\ApiResponseService;
use App\CodeAssignmentDistance\Service\DistanceCalculationService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DistanceCalculationServiceTest extends WebTestCase
{
    public DistanceCalculationService $distanceCalculationService;
    private ApiResponseService $apiResponseService;

    public function setUp(): void
    {
        $this->apiResponseService = $this->createMock(ApiResponseService::class);

        $this->distanceCalculationService = new DistanceCalculationService(
            json_decode($_ENV['LIST_OF_ADDRESS'], true),
            $_ENV['GOOGLE_MAP_API_HOST'],
            $_ENV['GOOGLE_GEO_CODE_HOST'],
            $_ENV['GOOGLE_MAP_API_KEY'],
            json_decode($_ENV['DESTINATION_ADDRESS'], true),
            $this->apiResponseService
        );
    }

    /**
     * @test
     */
    public function testFetchAPIResponse(
    ): void {
        // call DistanceCalculationService
        $serviceResult = $this->distanceCalculationService
            ->fetchAPIResponse();
        $this->assertIsArray($serviceResult);
    }
}