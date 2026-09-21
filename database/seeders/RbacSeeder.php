<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RbacSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Khởi tạo danh sách Permissions chuẩn E-commerce
        $permissions = [
            // Product management
            ['name' => 'products.view',   'display_name' => 'Xem danh sách sản phẩm'],
            ['name' => 'products.create', 'display_name' => 'Tạo sản phẩm mới'],
            ['name' => 'products.update', 'display_name' => 'Cập nhật sản phẩm'],
            ['name' => 'products.delete', 'display_name' => 'Xóa sản phẩm'],

            // Order management
            ['name' => 'orders.view',     'display_name' => 'Xem danh sách đơn hàng'],
            ['name' => 'orders.update',   'display_name' => 'Cập nhật trạng thái đơn hàng'],
            ['name' => 'orders.delete',   'display_name' => 'Hủy/Xóa đơn hàng'],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm['name']], $perm);
            // check xem record tòn tại trong db chưa
        }

        // 2. Tạo Roles
        $adminRole = Role::firstOrCreate(['name' => 'admin'], ['display_name' => 'Quản trị viên']);
        $staffRole = Role::firstOrCreate(['name' => 'staff'], ['display_name' => 'Nhân viên bán hàng']);
        $customerRole = Role::firstOrCreate(['name' => 'customer'], ['display_name' => 'Khách hàng']);

        // 3. Gán Permissions cho Roles
        // Admin: Toàn quyền
        $allPermissions = Permission::all();
        $adminRole->permissions()->sync($allPermissions->pluck('id'));

        // Staff: Chỉ có quyền xem/tạo/sửa sản phẩm và xem/sửa đơn hàng
        $staffPermissions = Permission::whereIn('name', [
            'products.view',
            'products.create',
            'products.update',
            'orders.view',
            'orders.update'
        ])->get();
        $staffRole->permissions()->sync($staffPermissions->pluck('id'));

        // Customer: Không cần cấp quyền Admin API

        // 4. Tạo các tài khoản Test cố định
        // Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            ['name' => 'System Admin', 'password' => Hash::make('12345678')]
        );
        $admin->roles()->sync([$adminRole->id]);

        // Staff User
        $staff = User::firstOrCreate(
            ['email' => 'staff@gmail.com'],
            ['name' => 'Sales Staff', 'password' => Hash::make('12345678')]
        );
        $staff->roles()->sync([$staffRole->id]);

        // Customer User
        $customer = User::firstOrCreate(
            ['email' => 'customer@gmail.com'],
            ['name' => 'Regular Customer', 'password' => Hash::make('12345678')]
        );
        $customer->roles()->sync([$customerRole->id]);

        // 5. Tạo thêm 10 User ngẫu nhiên bằng Factory
        User::factory(10)->create()->each(function ($user) use ($customerRole) {
            $user->roles()->sync([$customerRole->id]);
        });
    }
}
