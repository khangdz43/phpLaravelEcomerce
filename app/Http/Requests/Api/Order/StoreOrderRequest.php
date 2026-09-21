<?php

namespace App\Http\Requests\Api\Order;

use App\Traits\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class StoreOrderRequest extends FormRequest
{
    use ApiResponse;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_name'      => ['required', 'string', 'max:255'],
            'customer_email'     => ['required', 'email', 'max:255'],
            'customer_phone'     => ['required', 'string', 'max:20'],
            'shipping_address'   => ['required', 'string'],
            'items'              => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'distinct', 'exists:products,id'],
            'items.*.quantity'   => ['required', 'integer', 'min:1'],
        ];
    }
    public function messages(): array
    {
        return [
            // Thông tin khách hàng
            'customer_name.required'    => 'Vui lòng nhập tên người nhận hàng.',
            'customer_name.max'         => 'Tên người nhận không được vượt quá 255 ký tự.',

            'customer_email.required'   => 'Vui lòng nhập email nhận thông tin đơn hàng.',
            'customer_email.email'      => 'Định dạng email không hợp lệ.',
            'customer_email.max'        => 'Email không được vượt quá 255 ký tự.',

            'customer_phone.required'   => 'Vui lòng nhập số điện thoại liên hệ.',
            'customer_phone.max'        => 'Số điện thoại không vượt quá 20 ký tự.',

            'shipping_address.required' => 'Vui lòng nhập địa chỉ giao hàng.',

            // Danh sách sản phẩm trong đơn
            'items.required'            => 'Đơn hàng phải chứa ít nhất một sản phẩm.',
            'items.array'               => 'Danh sách sản phẩm phải là một mảng dữ liệu.',
            'items.min'                 => 'Đơn hàng phải có ít nhất :min sản phẩm.',

            // Chi tiết từng Item trong mảng items.*
            'items.*.product_id.required' => 'Mã sản phẩm (product_id) không được để trống.',
            'items.*.product_id.integer'  => 'Mã sản phẩm phải là số nguyên.',
            'items.*.product_id.exists'   => 'Sản phẩm chọn mua không tồn tại trên hệ thống.',

            'items.*.quantity.required'   => 'Số lượng mua không được để trống.',
            'items.*.quantity.integer'    => 'Số lượng mua phải là số nguyên.',
            'items.*.quantity.min'        => 'Số lượng mua tối thiểu cho mỗi sản phẩm là :min.',
        ];
    }
}
