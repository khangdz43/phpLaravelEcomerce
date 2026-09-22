<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;


class ProductPolicy
{

    // nếu là admin cho hết thì dùng hàm before


    // Hàm before chạy TRƯỚC TẤT CẢ các hàm update, delete...
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasPermission('admin.super')) {
            return true; // Cho qua luôn mà không cần check logic dưới
        }

        return null; // Nếu không phải admin thì chạy tiếp xuống các hàm update/delete bên dưới
    }

    
    public function update(User $user, Product $product): bool
    {
        return $user->hasPermission('products.update');
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->hasPermission('products.delete');
    }
}
