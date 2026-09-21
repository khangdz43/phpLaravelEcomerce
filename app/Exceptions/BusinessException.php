<?php

namespace App\Exceptions;

use RuntimeException;

abstract class BusinessException extends RuntimeException
{
    public function __construct(
        string $message,
        public readonly string $errorCode,
        public readonly int $statusCode,
        public readonly array $errors = [],
    ) {
        parent::__construct($message);
    }
}
