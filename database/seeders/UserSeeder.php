<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('name', 'admin')->first();
        $customerRole = Role::where('name', 'customer')->first();

        // Admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        if ($adminRole) {
            $admin->roles()->syncWithoutDetaching([$adminRole->id]);
        }

        // Staff user
        $staff = User::firstOrCreate(
            ['email' => 'staff@ecommerce.test'],
            [
                'name' => 'Staff Member',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        if ($adminRole) {
            $staff->roles()->syncWithoutDetaching([$adminRole->id]);
        }

        // Regular customers
        $customers = [
            ['name' => 'Nguyễn Văn An', 'email' => 'an@example.com'],
            ['name' => 'Trần Thị Bình', 'email' => 'binh@example.com'],
            ['name' => 'Lê Minh Cường', 'email' => 'cuong@example.com'],
            ['name' => 'Phạm Thu Dung', 'email' => 'dung@example.com'],
            ['name' => 'Hoàng Văn Em', 'email' => 'em@example.com'],
        ];

        foreach ($customers as $customer) {
            $user = User::firstOrCreate(
                ['email' => $customer['email']],
                [
                    'name' => $customer['name'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );
            if ($customerRole) {
                $user->roles()->syncWithoutDetaching([$customerRole->id]);
            }
        }

        $this->command->info('✅ UserSeeder: Tạo ' . (count($customers) + 2) . ' users thành công.');
    }
}
