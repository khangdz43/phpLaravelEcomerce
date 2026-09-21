<?php

namespace App\Repositories\Contracts;

use App\Models\Order;

interface OrderRepositoryInterface
{
    public function create(array $data): Order;
    public function createItems(Order $order, array $items): void;
}
