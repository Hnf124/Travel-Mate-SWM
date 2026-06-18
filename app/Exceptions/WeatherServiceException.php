<?php

namespace App\Exceptions;

use RuntimeException;

class WeatherServiceException extends RuntimeException
{
    public function __construct(
        string $message,
        protected int $statusCode = 502
    ) {
        parent::__construct($message);
    }

    public function statusCode(): int
    {
        return $this->statusCode;
    }
}
