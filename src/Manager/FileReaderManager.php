<?php

declare(strict_types=1);

namespace App\CodeAssignmentDistance\Manager;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class FileReaderManager
{
    protected const FILE_NAME = 'distance.csv';
    public function convertRequiredFormat(array $result)
    {
        return $this->toCSV($result);
    }

    /**
     * This method will convert csv format
     *
     * @param $orderView
     * @return Response
     */
    public function toCSV($result)
    {
        $encoders = [new CsvEncoder()];
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        $csvContent = $serializer->serialize($result, 'csv');

        $response = new Response($csvContent);
        $response->headers->set('Content-Encoding', 'UTF-8');
        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename=' . self::FILE_NAME);

        return $response;
    }
}
