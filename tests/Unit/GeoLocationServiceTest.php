<?php

declare(strict_types=1);

namespace App\CodeAssignmentDistance\Tests\Unit;

use App\CodeAssignmentDistance\Service\ApiClient;
use App\CodeAssignmentDistance\Service\ApiResponseService;
use App\CodeAssignmentDistance\Service\GeoLocationService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GeoLocationServiceTest extends WebTestCase
{
    public ApiResponseService $apiResponseService;
    private GeoLocationService $geoLocationService;

    public function setUp(): void
    {
        $this->apiClient = $this->createMock(ApiClient::class);
        $this->client = static::createClient();
        $this->apiResponseService = $this->createMock(ApiResponseService::class);

        $this->geoLocationService = new GeoLocationService(
            $_ENV['GOOGLE_MAP_API_HOST'],
            $_ENV['GOOGLE_GEO_CODE_HOST'],
            $_ENV['GOOGLE_MAP_API_KEY'],
            json_decode($_ENV['DESTINATION_ADDRESS'], true),
            json_decode($_ENV['LIST_OF_ADDRESS'], true),
            $this->apiResponseService
        );
    }

    /**
     * @test
     * @dataProvider getGetGeoLocationDataProvider
     */
    public function testGetGeoLocation(array $listOfAddress): void
    {
        $container = $this->client->getContainer();
        // call GeoLocation Service
        foreach ($listOfAddress as $address) {
            $serviceResult = $container->get(GeoLocationService::class)->getGeoLocation($address);
            $this->assertIsArray($serviceResult);
        }
    }

    /**
     * @test
     * @dataProvider getDistanceMatrixResultDataProvider
     */
    public function testGetDistanceMatrixResult(
        string $destinationLatitudeLongitude,
        string $originLatitudeLongitudeValue
    ): void {
        $container = $this->client->getContainer();
        // call GeoLocation Service
        $serviceResult = $container->get(GeoLocationService::class)
            ->getDistanceMatrixResult($destinationLatitudeLongitude, $originLatitudeLongitudeValue);
        $this->assertIsArray($serviceResult);
        $resultBody = $serviceResult['body'];
        $this->assertIsArray($resultBody);
        $this->assertIsArray($resultBody['rows']);
        $this->assertIsArray($resultBody['destination_addresses']);
        $this->assertIsArray($resultBody['origin_addresses']);
        $this->assertEquals(
            "Sint Janssingel 92, 5211 DA 's-Hertogenbosch, Netherlands",
            $resultBody['destination_addresses'][0]
        );
        $this->assertEquals(
            "Deldenerstraat 70, 7551 AH Hengelo, Netherlands",
            $resultBody['origin_addresses'][0]
        );
    }

    public function testDestinationLatitudeLongitudeValues(): void
    {
        $container = $this->client->getContainer();
        // call GeoLocation Service
        $serviceResult = $container->get(GeoLocationService::class)
            ->destinationLatitudeLongitudeValues();
        $this->assertIsArray($serviceResult);
        $this->assertEquals("51.6878748", $serviceResult['lat']);
        $this->assertEquals("5.2988689", $serviceResult['lng']);
    }

    public static function getDistanceMatrixResultDataProvider(): array
    {
        return [
            [
                '51.6878748,5.2988689',
                '52.2663601,6.7858158'
            ]
        ];
    }
    public static function getGetGeoLocationDataProvider(): array
    {
        return [
            [
                [
                    'Deldenerstraat 70, 7551AH Hengelo, The Netherlands',
                    '46/1 Office no 1 Ground Floor, Dada House, Inside dada silk mills compound, Udhana Main Rd, near Chhaydo Hospital, Surat, 394210, India',
                    'Weena 505, 3013 AL Rotterdam, The Netherlands',
                    '221B Baker St., London, United Kingdom',
                    '1600 Pennsylvania Avenue, Washington, D.C., USA',
                    '350 Fifth Avenue, New York City, NY 10118, USA',
                    'Saint Martha House, 00120 Citta del Vaticano, Vatican City',
                    '5225 Figueroa Mountain Road, Los Olivos, Calif. 93441, USA'
                ]
            ]
        ];
    }
}
