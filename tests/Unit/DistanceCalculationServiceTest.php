<?php

declare(strict_types=1);

namespace App\CodeAssignmentDistance\Tests\Unit;

use App\CodeAssignmentDistance\Service\ApiClient;
use App\CodeAssignmentDistance\Service\DistanceCalculationService;
use App\CodeAssignmentDistance\Service\GeoLocationService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class DistanceCalculationServiceTest extends WebTestCase
{
    public DistanceCalculationService $distanceCalculationService;
    private GeoLocationService $geoLocationService;

    public function setUp(): void
    {
        $this->apiClient = $this->createMock(ApiClient::class);
        $this->client = static::createClient();
        $this->geoLocationService = $this->createMock(GeoLocationService::class);
        $this->distanceCalculationService = new DistanceCalculationService(
            json_decode($_ENV['LIST_OF_ADDRESS'], true),
            $this->geoLocationService
        );
    }

    /**
     * @test
     * @dataProvider getFetchAPIResponseDataProvider
     */
    public function testFetchAPIResponse(): void
    {

        $container = $this->client->getContainer();
        // call DistanceCalculation Service
        $serviceResult = $container->get(DistanceCalculationService::class)->fetchAPIResponse();
        $this->assertIsArray($serviceResult);
        $listOfAddress = json_decode($_ENV['LIST_OF_ADDRESS'], true);
        foreach ($listOfAddress as $originAddress) {
            $address = $originAddress['name'];
            $this->assertIsArray($serviceResult[$address]);
            $this->assertEquals(Response::HTTP_OK, $serviceResult[$address]['statusCode']);
            $this->assertIsArray($serviceResult[$address]['body']);
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
