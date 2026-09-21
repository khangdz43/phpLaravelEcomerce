<?php

namespace App\Repositories\Contracts;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ProductRepositoryInterface
{
    public function searchPublished(array $filters = []): LengthAwarePaginator;
    
    public function findByIdOrFail(int $id): Product;
    public function findBySlugOrFail(string $slug): Product;
    public function create(array $data): Product;
    public function update(Product $product, array $data): Product;
    public function delete(Product $product): bool;
    public function slugExists(string $slug, ?int $ignoreId = null): bool;
    public function findAndLock(int $id): ?Product;
    public function decrementStock(Product $product, int $quantity): bool;
}
