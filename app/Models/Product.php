<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'sku',
        'description',
        'price',
        'sale_price',
        'stock',
        'status',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // quan hệ 1 :N
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function scopePriceBetween(Builder $query, ?int $minimum, ?int $maximum): Builder
    {
        return $query
            ->when($minimum !== null, fn(Builder $query) => $query->where('price', '>=', $minimum))
            ->when($maximum !== null, fn(Builder $query) => $query->where('price', '<=', $maximum));
    }
}
