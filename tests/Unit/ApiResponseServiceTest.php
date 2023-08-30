<?php

declare(strict_types=1);

namespace App\CodeAssignmentDistance\Tests\Unit;

use App\CodeAssignmentDistance\Service\ApiClient;
use App\CodeAssignmentDistance\Service\ApiResponseService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

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

    /**
     * @test
     * @dataProvider getFetchAPIResponseDataProvider
     */
    public function testFetchAPIResponse(string $url): void
    {
        $container = $this->client->getContainer();
        // call ApiResponseService Service
        $serviceResult = $container->get(ApiResponseService::class)->fetchAPIResponse($url);
        $this->assertIsArray($serviceResult);
        $this->assertEquals(Response::HTTP_OK, $serviceResult['statusCode']);
        $this->assertIsArray($serviceResult['body']);
        if (isset($serviceResult['body']['result'])) {
            $this->assertIsArray($serviceResult['body']['result']);
            foreach ($serviceResult['body']['result'] as $result) {
                $this->assertIsArray($result);
            }
        }
    }

    public static function getFetchAPIResponseDataProvider(): array
    {
        return [
            [
                sprintf(
                    '%s?address=%s&key=%s',
                    $_ENV['GOOGLE_GEO_CODE_HOST'],
                    'Deldenerstraat 70, 7551AH Hengelo, The Netherlands',
                    $_ENV['GOOGLE_MAP_API_KEY']
                ),
                sprintf(
                    '%s?origins=%s&destinations=%s&key=%s&departure_time=%s',
                    $_ENV['GOOGLE_MAP_API_HOST'],
                    '52.2663601,6.7858158',
                    '51.6878748,5.2988689',
                    $_ENV['GOOGLE_MAP_API_KEY'],
                    'now'
                )
            ]
        ];
    }
}
