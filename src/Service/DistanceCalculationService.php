<?php

declare(strict_types=1);

namespace App\CodeAssignmentDistance\Service;

use Symfony\Component\HttpFoundation\JsonResponse;

class DistanceCalculationService
{
    public function __construct(
        public readonly array $listOfAddresses,
        public readonly string $googleMapApiHost,
        public readonly string $geoCodingApiHost,
        public readonly string $googleMapApiKey,
        public readonly array $destinationAddress,
        private readonly ApiResponseService $apiResponseService
    ) {
    }

    private function fetchAPIResponse(): array | JsonResponse
    {
        $apiResponseResult = [];
        $inputOriginAddresses = array_column($this->listOfAddresses, 'address');
        $destinationAddress = array_column($this->destinationAddress, 'address')[0];

        if (!$destinationAddress) {
            return new JsonResponse(['error' => 'Please set Destination Address in env.'], 401);
        }
        $errorAddress = [];
        //                $originGeocodingResponse = $this->apiResponseService->fetchAPIResponse($this->getGeoCodeUrl($originAddress));
        foreach ($inputOriginAddresses as $originAddress) {
            try {
                $apiResponseResult[] = $this->apiResponseService->fetchAPIResponse($this->getDistanceMatrixUrl($originAddress, $destinationAddress));
            } catch (\InvalidArgumentException $e) {
                $errorAddress[] = $originAddress;
//                return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

        return [
            'apiResponseResult' => $apiResponseResult,
            'errorAddress' => $errorAddress,
        ];
    }

//    public function fetchGeoCode(string $url)
//    {
//        // Get geolocation for the origin address
//        $originGeocodingResponse = $geocodingClient->get('https://maps.googleapis.com/maps/api/geocode/json', [
//            'query' => ['address' => $originAddress, 'key' => $apiKey],
//        ]);
//        $originGeocodingData = json_decode($originGeocodingResponse->getBody()->getContents());
//    }

    public function getGeoCodeUrl(string $address):string
    {
        return sprintf(
            '%s?address=%s&key=%s',
            $this->geoCodingApiHost,
            $address,
            $this->googleMapApiKey
        );
    }

    public function getDistanceMatrixUrl(string $address, string $destinationAddress): string
    {
        return sprintf(
            '%s?origins=%s&destinations=%s&key=%s',
            $this->googleMapApiHost,
            $address,
            $destinationAddress,
            $this->googleMapApiKey
        );
    }

    public function calculateDistance(): array
    {
        $results = [];
        $apiResponseResult = $this->fetchAPIResponse()['apiResponseResult'] ?? [];

        foreach ($apiResponseResult as $response) {
            $responseBody = $response['body'];
            if (
                $responseBody['status'] === 'OK' &&
                isset($responseBody['rows'][0]['elements'][0]['distance']['value']))
            {
                $distanceValue = $responseBody['rows'][0]['elements'][0]['distance']['value'];
                $distance = sprintf('%.2f km', $distanceValue / 1000);
                $originAddress = $responseBody['origin_addresses'][0] ?? '';
                $results[] = [
                    'distance' => $distance,
                    'name' => $this->getAddressName($originAddress),
                    'origin_address' => $originAddress,
                ];
            }
        }

        return $results;
    }

    private function getAddressName(string $targetAddress): string
    {
        foreach ($this->listOfAddresses as $address) {
            if ($address['address'] === $targetAddress) {
                return $address['name'];
            }
        }
        return '';
    }
}