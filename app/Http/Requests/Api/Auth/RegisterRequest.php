<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'     => 'Họ và tên không được để trống.',
            'name.string'       => 'Họ và tên phải là chuỗi ký tự.',
            'name.max'          => 'Họ và tên không được vượt quá 255 ký tự.',

            'email.required'    => 'Địa chỉ email không được để trống.',
            'email.string'      => 'Email phải là chuỗi ký tự.',
            'email.email'       => 'Địa chỉ email không đúng định dạng.',
            'email.max'         => 'Email không được vượt quá 255 ký tự.',
            'email.unique'      => 'Địa chỉ email này đã tồn tại trên hệ thống.',

            'password.required' => 'Mật khẩu không được để trống.',
            'password.string'   => 'Mật khẩu phải là chuỗi ký tự.',
            'password.min'      => 'Mật khẩu phải có ít nhất 8 ký tự.',
        ];
    }
}
