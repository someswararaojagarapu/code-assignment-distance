<?php

declare(strict_types=1);

namespace App\CodeAssignmentDistance\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class DistanceController extends AbstractController
{
    #[Route('/calculate-distances', name: 'calculate-distances', methods: 'GET')]
    public function index(
        Request $request
    ): Response {

        return new Response('');
    }
}