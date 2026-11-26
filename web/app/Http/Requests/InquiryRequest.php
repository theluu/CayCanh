<?php

namespace App\Http\Requests;

use App\Enums\InquiryType;
use App\Models\Inquiry;
use App\Rules\VietnamesePhone;
use Closure;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'type' => ['required', Rule::enum(InquiryType::class)],
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20', new VietnamesePhone],
            'email' => ['nullable', 'email', 'max:150'],
            'address' => ['nullable', 'string', 'max:255'],
            'message' => ['nullable', 'string', 'max:2000'],
            'subject_type' => ['nullable', Rule::in(Inquiry::SUBJECTS)],
            'subject_id' => ['nullable', 'required_with:subject_type', 'integer', function (string $attribute, mixed $value, Closure $fail) {
                $type = $this->input('subject_type');
                $class = $type ? Relation::getMorphedModel($type) : null;

                if ($class && ! $class::whereKey($value)->exists()) {
                    $fail('Mục bạn chọn không còn tồn tại.');
                }
            }],
            'meta' => ['nullable', 'array:project_kind,location,area,budget,timeline'],
            'meta.*' => ['nullable', 'string', 'max:255'],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return ['name' => 'họ tên', 'phone' => 'số điện thoại', 'message' => 'nội dung'];
    }
}
