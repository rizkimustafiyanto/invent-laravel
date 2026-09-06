<?php

namespace App\Modules\Payment\Requests;

use App\Enums\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date' => ['required', 'string', 'max:255'],
            'sale_id' => ['required', 'uuid', 'exists:sales,id'],
            'payment_method' => ['required', Rule::enum(PaymentMethod::class)]
        ];
    }
}
