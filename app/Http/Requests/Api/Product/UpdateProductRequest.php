<?php

namespace App\Http\Requests\Api\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('products.update') ?? false;
    }

    public function rules(): array
    {
        // Lấy ID sản phẩm từ URL (route parameter)
        $productId = $this->route('product')?->id;

        return [
            // sometimes tức là truyền không đủ tham số cũng được nó giữ cái cũ
            'category_id' => ['sometimes', 'required', 'integer', 'exists:categories,id'],
            'name'        => ['sometimes', 'required', 'string', 'max:255'],
            // BỎ QUA kiểm tra unique cho chính ID đang sửa
            'sku'         => ['sometimes', 'required', 'string', 'max:50', Rule::unique('products', 'sku')->ignore($productId)],
            'price'       => ['sometimes', 'required', 'numeric', 'min:0'],
            'sale_price'  => ['nullable', 'numeric', 'min:0', 'lt:price'],
            'stock'       => ['sometimes', 'required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'status'      => ['sometimes', 'required', 'in:draft,published,out_of_stock'],
        ];
    }

    public function messages(): array
    {
        return [
            // category_id
            'category_id.required' => 'Vui lòng chọn danh mục sản phẩm.',
            'category_id.integer'  => 'Mã danh mục sản phẩm phải là số nguyên.',
            'category_id.exists'   => 'Danh mục sản phẩm được chọn không tồn tại trên hệ thống.',

            // name
            'name.required' => 'Tên sản phẩm không được để trống.',
            'name.string'   => 'Tên sản phẩm phải là chuỗi ký tự.',
            'name.max'      => 'Tên sản phẩm không được vượt quá 255 ký tự.',

            // sku
            'sku.required' => 'Mã SKU không được để trống.',
            'sku.string'   => 'Mã SKU phải là chuỗi ký tự.',
            'sku.max'      => 'Mã SKU không được vượt quá 50 ký tự.',
            'sku.unique'   => 'Mã SKU này đã được sử dụng bởi một sản phẩm khác.',

            // price
            'price.required' => 'Giá sản phẩm không được để trống.',
            'price.numeric'  => 'Giá sản phẩm phải là chữ số.',
            'price.min'      => 'Giá sản phẩm không được nhỏ hơn 0.',

            // sale_price
            'sale_price.numeric' => 'Giá khuyến mãi phải là chữ số.',
            'sale_price.min'     => 'Giá khuyến mãi không được nhỏ hơn 0.',
            'sale_price.lt'      => 'Giá khuyến mãi phải nhỏ hơn giá gốc của sản phẩm.',

            // stock
            'stock.required' => 'Số lượng tồn kho không được để trống.',
            'stock.integer'  => 'Số lượng tồn kho phải là số nguyên.',
            'stock.min'      => 'Số lượng tồn kho không được nhỏ hơn 0.',

            // status
            'status.required' => 'Trạng thái sản phẩm không được để trống.',
            'status.in'       => 'Trạng thái sản phẩm không hợp lệ (chỉ chấp nhận: draft, published, out_of_stock).',
        ];
    }
}
