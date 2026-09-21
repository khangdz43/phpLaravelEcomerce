<?php

namespace App\Services;

use App\Enums\PaymentProvider;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Str;

class PaymentService
{
    public function createForOrder(Order $order, string $provider = PaymentProvider::COD->value): Payment
    {
        $status = $provider === PaymentProvider::COD->value
            ? PaymentStatus::PENDING->value
            : PaymentStatus::PENDING->value;

        return $order->payments()->create([
            'provider' => $provider,
            'transaction_id' => strtoupper($provider).'-'.strtoupper(Str::random(10)),
            'amount' => (int) $order->total_amount,
            'status' => $status,
            'payload' => $provider === PaymentProvider::BANK_TRANSFER->value
                ? $this->bankInstructions($order)
                : ['method' => 'cash_on_delivery'],
        ]);
    }

    public function markPaid(Order $order): void
    {
        $order->payments()
            ->where('status', PaymentStatus::PENDING->value)
            ->update([
                'status' => PaymentStatus::PAID->value,
                'paid_at' => now(),
            ]);
    }

    public function bankInstructions(Order $order): array
    {
        return [
            'bank_name' => 'Vietcombank',
            'account_name' => 'CONG TY VIETEC',
            'account_number' => '0123456789',
            'transfer_content' => $order->order_code,
        ];
    }
}
