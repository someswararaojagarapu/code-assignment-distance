<?php

declare(strict_types=1);

namespace App\CodeAssignmentDistance\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\RequestStack;

class ApiClient
{
    private string $apiHost;

    private mixed $client;

    private mixed $request;

    private ?string $contentBody = null;

    public function __construct(
        private readonly ParameterBagInterface $params,
        protected RequestStack $requestStack,
        private readonly GetHeaderParameterService $getHeaderParameterService
    ) {
        $this->apiHost = $this->params->get('GOOGLE_MAP_API_HOST');

        $client = HttpClient::create();
        $this->request = $requestStack->getCurrentRequest();
        $this->setContent(true);
        $this->client = $client->withOptions([
            'base_uri' => $this->apiHost,
            'headers' => $this->getHeaderParameterService->getHeaders()
        ]);
    }

    public function doGetRequest(
        string $method,
        string $url,
        ?array $options = [],
        bool $passContentBody = true
    ): array {
        $query = ''; /** TODO */

        // In case request content body found then pass as body to distance API
        $requestOptions = $passContentBody && $this->contentBody
            ? ['body' => $this->contentBody]
            : ['json' => $options];

        $response = $this->client->request(
            $method,
            sprintf('%s?%s', $url, $query),
            $requestOptions
        );

        return [
            'statusCode' => $response->getStatusCode(),
            'headers' => $response->getHeaders(false),
            'body' => $response->getContent(false),
        ];
    }

    private function setContent(bool $defaultContent = true, array $body = []): void
    {
        if ($defaultContent) {
            if ($this->request->getContent()) {
                $this->contentBody = $this->request->getContent();
            }
        } else {
            $this->contentBody = json_encode($body);
        }
    }

    private function getContent(): string | null
    {
        return $this->contentBody;
    }
}