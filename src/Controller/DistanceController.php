<?php

declare(strict_types=1);

namespace App\CodeAssignmentDistance\Controller;

use App\CodeAssignmentDistance\Exception\CustomApiException;
use App\CodeAssignmentDistance\Manager\FileReaderManager;
use App\CodeAssignmentDistance\Service\DistanceCalculationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class DistanceController extends AbstractController
{
    #[Route('/calculate-distances', name: 'calculate-distances', methods: 'GET')]
    public function calculateDistance(
        DistanceCalculationService $distanceCalculationService,
        FileReaderManager $fileReaderManager
    ): Response {
        try {
            $result = $distanceCalculationService->sortTheResultsByDistance();

            return $fileReaderManager->convertRequiredFormat($result);
        } catch (\Exception $e) {
            throw new CustomApiException($e->getMessage(), $e->getCode());
        }
    }
}
