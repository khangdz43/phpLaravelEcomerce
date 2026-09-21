<?php

namespace App\Services;

use App\Enums\CouponType;
use App\Exceptions\InvalidCouponException;
use App\Models\Coupon;
use App\Models\User;

class CouponService
{
    public function findUsable(string $code): Coupon
    {
        $coupon = Coupon::query()
            ->whereRaw('UPPER(code) = ?', [strtoupper(trim($code))])
            ->lockForUpdate()
            ->first();

        if (! $coupon || ! $coupon->is_active) {
            throw new InvalidCouponException();
        }

        if ($coupon->starts_at && $coupon->starts_at->isFuture()) {
            throw new InvalidCouponException('Mã giảm giá chưa đến thời gian áp dụng.');
        }

        if ($coupon->expires_at && $coupon->expires_at->isPast()) {
            throw new InvalidCouponException('Mã giảm giá đã hết hạn.');
        }

        if ($coupon->usage_limit !== null && $coupon->used_count >= $coupon->usage_limit) {
            throw new InvalidCouponException('Mã giảm giá đã hết lượt sử dụng.');
        }

        return $coupon;
    }

    public function discountFor(Coupon $coupon, int $subtotal): int
    {
        if ($subtotal < (int) $coupon->minimum_order_amount) {
            throw new InvalidCouponException('Đơn hàng chưa đạt giá trị tối thiểu để dùng mã này.');
        }

        $discount = $coupon->type === CouponType::PERCENT->value
            ? (int) floor($subtotal * ((int) $coupon->value) / 100)
            : (int) $coupon->value;

        return min($subtotal, max(0, $discount));
    }

    public function preview(string $code, int $subtotal, ?User $user = null): array
    {
        $coupon = $this->findUsable($code);
        $discount = $this->discountFor($coupon, $subtotal);

        return [
            'code' => $coupon->code,
            'type' => $coupon->type,
            'discount_amount' => $discount,
            'payable_amount' => $subtotal - $discount,
        ];
    }
}
