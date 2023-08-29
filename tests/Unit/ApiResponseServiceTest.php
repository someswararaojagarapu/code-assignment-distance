<?php

declare(strict_types=1);

namespace App\CodeAssignmentDistance\Tests\Unit;

use App\CodeAssignmentDistance\Service\ApiClient;
use App\CodeAssignmentDistance\Service\ApiResponseService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiResponseServiceTest extends WebTestCase
{
    public ApiResponseService $apiResponseService;
    private ApiClient $apiClient;

    public function setUp(): void
    {
        $this->apiClient = $this->createMock(ApiClient::class);
        $this->client = static::createClient();
        $this->apiResponseService = new ApiResponseService($this->apiClient);
    }

    public function testFetchAPIResponse(
    ): void {
        $url = sprintf(
            '%s?origins=%s&destinations=%s&key=%s',
            $_ENV['GOOGLE_MAP_API_HOST'],
            'Deldenerstraat 70, 7551AH Hengelo, The Netherlands',
            'Sint Janssingel 92, 5211 DA s-Hertogenbosch, The Netherlands',
            $_ENV['GOOGLE_MAP_API_KEY']
        );

        // call ApiResponseService Service
        $serviceResult = $this->apiResponseService
            ->fetchAPIResponse($url);
        $this->assertIsArray($serviceResult);
    }
}