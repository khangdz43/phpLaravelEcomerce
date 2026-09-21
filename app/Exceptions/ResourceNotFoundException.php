<?php
namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class ResourceNotFoundException extends BusinessException
{
    public function __construct(
        string $message = "Tài nguyên không tồn tại trên hệ thống.",
        string $errorCode = 'RESOURCE_NOT_FOUND'
    ) {
        parent::__construct(
            message: $message,
            errorCode: $errorCode,
            statusCode: Response::HTTP_NOT_FOUND // 404
        );
    }
}