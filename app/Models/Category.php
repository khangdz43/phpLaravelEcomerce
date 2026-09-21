<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory; // trait này tìm đúng tên file [ModelName]Factory để thực thi cái seed
    
    // $fillable: chỉ cho chèn dữ liệu 4 cột này nếu ai đó thêm vào payload thì k ghi vào db
    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'description',
        'is_active',
    ];


    // tức là khi gọi parent nó sẽ 
    // SELECT * FROM categories WHERE id = parent_id;
    // lấy danh mục cha
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    // SELECT * FROM categories WHERE parent_id = id;
    // lấy danh mục con
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    //SELECT * FROM products WHERE category_id = $category->id;
    // quan hệ 1 -N
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
