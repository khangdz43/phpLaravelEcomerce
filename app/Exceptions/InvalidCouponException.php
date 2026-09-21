<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class InvalidCouponException extends BusinessException
{
    public function __construct(string $message = 'Mã giảm giá không hợp lệ hoặc đã hết hạn.')
    {
        parent::__construct(
            $message,
            'INVALID_COUPON',
            Response::HTTP_UNPROCESSABLE_ENTITY,
        );
    }
}
