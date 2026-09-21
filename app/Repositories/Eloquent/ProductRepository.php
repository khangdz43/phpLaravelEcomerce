<?php

namespace App\Repositories\Eloquent;

use App\Exceptions\ResourceNotFoundException;
use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class ProductRepository implements ProductRepositoryInterface
{
    // xử lí tìm kiếm động
    public function searchPublished(array $filters = []): LengthAwarePaginator
    {
        $query = Product::query()
            ->with('category:id,name,slug')
            ->published()
            ->when($filters['q'] ?? null, function (Builder $query, string $search): void {
                $like = '%' . addcslashes($search, '%_\\') . '%';

                $query->where(function (Builder $query) use ($like): void {
                    $query->where('name', 'like', $like)
                        ->orWhere('sku', 'like', $like)
                        ->orWhere('description', 'like', $like);
                });
            })
            ->when($filters['category_id'] ?? null, fn(Builder $query, int $categoryId) =>
            $query->where('category_id', $categoryId))
            ->priceBetween($filters['min_price'] ?? null, $filters['max_price'] ?? null);

        match ($filters['sort'] ?? 'newest') {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'name_asc' => $query->orderBy('name'),
            default => $query->latest('id'),
        };

        return $query
            ->paginate($filters['per_page'] ?? 10)
            ->withQueryString();
    }

    public function findByIdOrFail(int $id): Product
    {
        $product = Product::find($id);

        if (!$product) {
            throw new ResourceNotFoundException("Sản phẩm không tồn tại"); // Bắn trực tiếp Custom Exception
        }

        return $product;
    }

    public function findBySlugOrFail(string $slug): Product
    {
        return Product::with('category:id,name,slug')
            ->where('slug', $slug)
            ->firstOrFail();
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(Product $product, array $data): Product
    {
        $product->update($data);

        return $product->refresh();
    }

    public function delete(Product $product): bool
    {
        return (bool) $product->delete();
    }

    public function slugExists(string $slug, ?int $ignoreId = null): bool
    {
        return Product::where('slug', $slug)
            ->when($ignoreId !== null, fn($query) => $query->where('id', '<>', $ignoreId))
            ->exists();
    }

    public function findAndLock(int $id): ?Product
    {
        return Product::where('id', $id)->lockForUpdate()->first();
    }

    public function decrementStock(Product $product, int $quantity): bool
    {
        return $product->decrement('stock', $quantity);
    }

    public function incrementStock(Product $product, int $quantity): bool
    {
        return $product->increment('stock', $quantity);
    }
}
