<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class InsufficientStockException extends BusinessException
{
    public function __construct(string $productName)
    {
        parent::__construct(
            "Sản phẩm [{$productName}] đã hết hàng hoặc không đủ số lượng tồn kho.",
            'INSUFFICIENT_STOCK',
            Response::HTTP_CONFLICT,
        );
    }
}
