<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class ProductService
{
    public function __construct(
        protected ProductRepositoryInterface $productRepo
    ) {}

    public function searchPublished(array $filters = []): LengthAwarePaginator
    {
        return $this->productRepo->searchPublished($filters);
    }

    public function findById(int $id): Product
    {
        return $this->productRepo->findByIdOrFail($id);
    }

    public function findBySlug(string $slug): Product
    {
        return $this->productRepo->findBySlugOrFail($slug);
    }

    public function create(array $data): Product
    {
        $data['slug'] = $this->createUniqueSlug($data['name']);

        return $this->productRepo->create($data);
    }

    public function update(Product $product, array $data): Product
    {

        if (isset($data['name']) && $data['name'] !== $product->name) {
            $data['slug'] = $this->createUniqueSlug($data['name'], $product->id);
        }

        return $this->productRepo->update($product, $data);
    }

    public function deleteById(int $id): bool
    {
        return $this->productRepo->delete($this->findById($id));
    }

    private function createUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $originalSlug = Str::slug($name);
        $slug = $originalSlug;
        $count = 1;

        while ($this->productRepo->slugExists($slug, $ignoreId)) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        return $slug;
    }
}
