<?php

declare(strict_types=1);

namespace App\CodeAssignmentDistance\Tests\Feature;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class DistanceCalculatorTest extends WebTestCase
{
    private const DISTANCE_CALCULATOR_API = '/api/calculate-distances';
    private const DESTINATION_ADDRESS = "Sint Janssingel 92, 5211 DA s-Hertogenbosch, The Netherlands";

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    /**
     * @test
     * @dataProvider calculateDistanceDataProvider
     */
    public function testcalculateDistance(
        string $destinationAddress,
        array $originAddress,
        int $statusCode,
        string $contentType
    ): void {
        $this->client->request(
            'GET',
            self::DISTANCE_CALCULATOR_API
        );
        $response = $this->client->getResponse();
        $this->assertEquals($statusCode, $response->getStatusCode());
        $this->assertResponseHeaderSame('content-type', $contentType);

        // Validate a successful response and some content
        $this->assertResponseIsSuccessful();
    }

    public static function calculateDistanceDataProvider(): array
    {
        $originAddress = [
            [
                "name" => "Eastern Enterprise B.V.",
                "address" => "Deldenerstraat 70, 7551AH Hengelo, The Netherlands"
            ],
            [
                "name" => "Eastern Enterprise",
                "address" => "46/1 Office no 1 Ground Floor, Dada House, Inside dada silk mills compound, Udhana Main Rd, near Chhaydo Hospital, Surat, 394210, India"
            ],
            [
                "name" => "Adchieve Rotterdam",
                "address" => "Weena 505, 3013 AL Rotterdam, The Netherlands"
            ],
            [
                "name" => "Sherlock Holmes",
                "address" => "221B Baker St., London, United Kingdom"
            ],
            [
                "name" => "The White House",
                "address" => "1600 Pennsylvania Avenue, Washington, D.C., USA"
            ],
            [
                "name" => "The Empire State Building",
                "address" => "350 Fifth Avenue, New York City, NY 10118, USA"
            ],
            [
                "name" => "The Pope",
                "address" => "Saint Martha House, 00120 Citta del Vaticano, Vatican City"
            ],
            [
                "name" => "Neverland",
                "address" => "5225 Figueroa Mountain Road, Los Olivos, Calif. 93441, USA"
            ]
        ];

        return [
            'Success scenario' => [
                self::DESTINATION_ADDRESS,
                $originAddress,
                Response::HTTP_OK,
                'text/csv; charset=UTF-8'
            ]
        ];
    }
}