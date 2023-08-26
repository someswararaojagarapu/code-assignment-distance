<?php

declare(strict_types=1);

namespace App\CodeAssignmentDistance\Service;

use Symfony\Component\HttpFoundation\RequestStack;

class GetHeaderParameterService
{
    private mixed $request;
    public function __construct(
        protected RequestStack $requestStack
    ) {
        $this->request = $requestStack->getCurrentRequest();
    }

    public function getHeaders(): array
    {
        $headers = $this->request->headers->all();
        $requiredHeaders = ['authorization', 'content-type'];
        return array_filter($headers, function ($key) use ($requiredHeaders) {
            return in_array($key, $requiredHeaders);
        }, ARRAY_FILTER_USE_KEY);
    }
}