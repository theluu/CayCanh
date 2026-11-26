<?php

namespace App\Http\Requests;

use App\Enums\PaymentMethod;
use App\Rules\VietnamesePhone;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20', new VietnamesePhone],
            'email' => ['nullable', 'email', 'max:150'],
            'province' => ['required', 'string', 'max:100'],
            'address' => ['required', 'string', 'max:255'],
            'note' => ['nullable', 'string', 'max:1000'],
            'payment_method' => ['required', Rule::enum(PaymentMethod::class)],
            'expected_total' => ['nullable', 'integer', 'min:0'],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'customer_name' => 'họ tên',
            'phone' => 'số điện thoại',
            'province' => 'tỉnh/thành',
            'address' => 'địa chỉ',
            'payment_method' => 'phương thức thanh toán',
        ];
    }
}
