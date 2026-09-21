<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class InvalidCredentialsException extends BusinessException
{
    public function __construct(string $message = 'Thông tin đăng nhập không chính xác.')
    {
        parent::__construct(
            message: $message,
            errorCode: 'INVALID_CREDENTIALS',
            statusCode: Response::HTTP_UNAUTHORIZED
        );
    }
}
