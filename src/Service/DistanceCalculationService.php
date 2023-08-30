<?php

declare(strict_types=1);

namespace App\CodeAssignmentDistance\Service;

use Symfony\Component\HttpFoundation\JsonResponse;

class DistanceCalculationService
{
    public function __construct(
        public readonly array $listOfAddresses,
        private readonly GeoLocationService $geoLocationService
    ) {
    }

    public function sortTheResultsByDistance(): array
    {
        $result = $this->calculateDistance();
        $noPathFoundResults = [];
        $kmResults = [];

        foreach ($result as $entry) {
            match ($entry['distance']) {
            "No Path Found" => $noPathFoundResults[] = $entry,
                    default => $kmResults[] = $entry,
                };
        }
        usort(
            $kmResults,
            fn($a, $b) =>
                (float) str_replace(',', '', $a['distance'])
                <=>
                (float) str_replace(',', '', $b['distance'])
            );

        foreach ($kmResults as &$item) {
            $item['distance'] = number_format(floatval($item['distance']), 2) . ' km';
        }

        $distanceResult = [...$kmResults, ...$noPathFoundResults];
        $sortNumber = 1;
        foreach ($distanceResult as &$item) {
            $item['sortnumber'] = $sortNumber;
            $sortNumber++;
        }

        return $distanceResult;
    }

    public function fetchAPIResponse(): array | JsonResponse
    {
        $apiResponseResult = [];
        $destinationLatitudeLongitudeValues = $this->geoLocationService->destinationLatitudeLongitudeValues();
        $destinationLatitudeLongitude = "{$destinationLatitudeLongitudeValues['lat']},{$destinationLatitudeLongitudeValues['lng']}";
        $originLatitudeLongitudeValues = $this->getOriginLatitudeLongitudeValues();
        foreach ($originLatitudeLongitudeValues as $name => $originLatitudeLongitudeValue) {
            try {
                $originLatitudeLongitudeValue = "{$originLatitudeLongitudeValue['lat']},{$originLatitudeLongitudeValue['lng']}";
                $apiResponseResult[$name] = $this->geoLocationService->getDistanceMatrixResult(
                    $destinationLatitudeLongitude,
                    $originLatitudeLongitudeValue
                );
            } catch (\InvalidArgumentException $e) {
                return ['code' => $e->getCode(), 'errorMessage' => $e->getMessage()];
            }
        }

        return $apiResponseResult;
    }

    public function getOriginLatitudeLongitudeValues(): array
    {
        $originAddressResult = [];

        foreach ($this->listOfAddresses as $originNameAndAddress) {
            try {
                $name = $originNameAndAddress['name'] ?? '';
                $originAddress = $originNameAndAddress['address'] ?? '';
                $geoLocation = $this->geoLocationService->getGeoLocation($originAddress);
                $originAddressResult[$name] = ['lat' => $geoLocation['lat'], 'lng' => $geoLocation['lng']];
            } catch (\InvalidArgumentException $e) {
                return ['code' => $e->getCode(), 'errorMessage' => $e->getMessage()];
            }
        }

        return $originAddressResult;
    }

    public function calculateDistance(): array
    {
        $results = [];
        $apiResponseResult = $this->fetchAPIResponse() ?? [];

        foreach ($apiResponseResult as $name => $response) {
            $responseBody = $response['body'];
            $originAddress = $responseBody['origin_addresses'][0] ?? '';
            $status = $responseBody['status'] ?? '';

            if ($status === 'OK' && isset($responseBody['rows'][0]['elements'][0]['distance']['value'])) {
                $distanceValue = $responseBody['rows'][0]['elements'][0]['distance']['value'];
                $distance = bcdiv((string) $distanceValue, '1000', 5);
            } else {
                $distance = 'No Path Found';
                $originAddress = $this->getOriginAddress($name);
            }

            $results[] = [
                'distance' => $distance,
                'name' => $name,
                'origin_address' => $originAddress,
            ];
        }

        return $results;
    }

    private function getOriginAddress(string $targetName): string
    {
    foreach ($this->listOfAddresses as $item) {
        if ($item['name'] === $targetName) {
            return $item['address'];
        }
    }
    return '';
    }
}
