<?php

namespace App\Http\Requests\Api\Product;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\SalePriceBelowPrice;

class StoreProductRequest extends FormRequest
{
    // 1. Phân quyền truy cập (Ủy quyền)
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('products.create') ?? false;
    }

    // 2. Định nghĩa các quy tắc kiểm tra dữ liệu
    public function rules(): array
    {
        return [
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name'        => ['required', 'string', 'max:255'],
            'sku'         => ['required', 'string', 'max:50', 'unique:products,sku'],
            'price'       => ['required', 'integer', 'min:0'],
            'sale_price'  => ['nullable', 'integer', 'min:0', new SalePriceBelowPrice((int) $this->input('price'))],
            'stock'       => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'status'      => ['required', 'in:draft,published,out_of_stock'],
        ];
    }

    // message này để ném exception
    public function messages(): array
    {
        return [
            'sku.unique' => 'Sản phẩm với mã SKU này đã tồn tại trong hệ thống.',
            'sku.required' => 'Vui lòng nhập mã SKU cho sản phẩm.',
            'category_id.exists' => 'Danh mục sản phẩm được chọn không tồn tại.',
        ];
    }
}
