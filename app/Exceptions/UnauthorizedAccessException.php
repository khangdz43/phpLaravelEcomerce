<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class UnauthorizedAccessException extends BusinessException
{
    public function __construct(string $message = 'Bạn không có quyền thực hiện hành động này.')
    {
        parent::__construct(
            message: $message,
            errorCode: 'FORBIDDEN_ACCESS',
            statusCode: Response::HTTP_FORBIDDEN // 403
        );
    }
}
