<?php

declare(strict_types=1);

namespace App\CodeAssignmentDistance\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class CustomApiException extends HttpException
{
    public function __construct($message, $statusCode)
    {
        parent::__construct($statusCode, $message);
    }
}