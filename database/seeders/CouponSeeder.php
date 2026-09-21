<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Coupon;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        $coupons = [
            [
                'code' => 'WELCOME10',
                'type' => 'percent',
                'value' => 10,
                'minimum_order_amount' => 200000,
                'usage_limit' => 1000,
                'used_count' => 0,
                'is_active' => true,
                'starts_at' => now(),
                'expires_at' => now()->addYear(),
            ],
            [
                'code' => 'SAVE50K',
                'type' => 'fixed',
                'value' => 50000,
                'minimum_order_amount' => 500000,
                'usage_limit' => 500,
                'used_count' => 0,
                'is_active' => true,
                'starts_at' => now(),
                'expires_at' => now()->addMonths(6),
            ],
            [
                'code' => 'FLASH20',
                'type' => 'percent',
                'value' => 20,
                'minimum_order_amount' => 500000,
                'usage_limit' => 200,
                'used_count' => 0,
                'is_active' => true,
                'starts_at' => now(),
                'expires_at' => now()->addDays(7),
            ],
            [
                'code' => 'VIP200K',
                'type' => 'fixed',
                'value' => 200000,
                'minimum_order_amount' => 2000000,
                'usage_limit' => 100,
                'used_count' => 0,
                'is_active' => true,
                'starts_at' => now(),
                'expires_at' => now()->addYear(),
            ],
            [
                'code' => 'TESTEXPIRED',
                'type' => 'percent',
                'value' => 15,
                'minimum_order_amount' => 0,
                'usage_limit' => 100,
                'used_count' => 0,
                'is_active' => true,
                'starts_at' => now()->subYear(),
                'expires_at' => now()->subDay(),
            ],
        ];

        foreach ($coupons as $coupon) {
            Coupon::firstOrCreate(
                ['code' => $coupon['code']],
                $coupon
            );
        }

        $this->command->info('✅ CouponSeeder: Tạo ' . count($coupons) . ' coupons thành công.');
    }
}
