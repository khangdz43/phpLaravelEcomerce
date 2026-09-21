<?php

namespace App\Enums;

enum PaymentProvider: string
{
    case COD = 'cod';
    case BANK_TRANSFER = 'bank_transfer';
}
