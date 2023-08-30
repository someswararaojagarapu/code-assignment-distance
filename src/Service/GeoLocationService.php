<?php

declare(strict_types=1);

namespace App\CodeAssignmentDistance\Service;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class GeoLocationService
{
    public function __construct(
        public readonly string $googleMapApiHost,
        public readonly string $geoCodingApiHost,
        public readonly string $googleMapApiKey,
        public readonly array $destinationAddress,
        public readonly array $listOfAddresses,
        private readonly ApiResponseService $apiResponseService
    ) {
    }

    public function getGeoLocation(string $originAddress): array
    {
        $response = $this->apiResponseService->fetchAPIResponse($this->getGeoCodeUrl($originAddress));
        $responseBody = $response['body'] ?? [];
        if (empty($responseBody['results'])) {
            throw new \RuntimeException('Geolocation not found.');
        }

        return $responseBody['results'][0]['geometry']['location'];
    }

    public function getDistanceMatrixResult(
        string $destinationLatitudeLongitude,
        string $originLatitudeLongitudeValue
    ): array {
        try {
            return $this->apiResponseService->fetchAPIResponse(
                $this->getDistanceMatrixUrl($destinationLatitudeLongitude, $originLatitudeLongitudeValue)
            );
        } catch (\InvalidArgumentException $e) {
            return ['code' => $e->getCode(), 'errorMessage' => $e->getMessage()];
        }
    }

    public function destinationLatitudeLongitudeValues(): array | JsonResponse
    {
        try {
            if (!($this->destinationAddress[0]['address'])) {
                return new JsonResponse(['error' => 'Please set Destination Address in env.'], 401);
            }
            return $this->getGeoLocation($this->destinationAddress[0]['address'] ?? '');
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getGeoCodeUrl(string $address): string
    {
        return sprintf(
            '%s?address=%s&key=%s',
            $this->geoCodingApiHost,
            $address,
            $this->googleMapApiKey
        );
    }

    public function getDistanceMatrixUrl(string $destinationLatitudeLongitude, string $originLatitudeLongitudeValue): string
    {
        $departureTime = 'now';

        return sprintf(
            '%s?origins=%s&destinations=%s&key=%s&departure_time=%s',
            $this->googleMapApiHost,
            $originLatitudeLongitudeValue,
            $destinationLatitudeLongitude,
            $this->googleMapApiKey,
            $departureTime
        );
    }
}
