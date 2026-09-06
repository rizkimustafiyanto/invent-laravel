<?php

namespace App\Modules\Payment\Requests;

use App\Enums\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date' => ['sometimes', 'string', 'max:255'],
            'sale_id' => ['sometimes', 'uuid', 'exists:sales,id'],
            'payment_method' => ['sometimes', Rule::enum(PaymentMethod::class)]
        ];
    }
}
