<?php

namespace App\Modules\Sale\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $saleId = $this->route('sale')?->id ?? $this->route('sale');

        return [
            'code' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('sales', 'code')->ignore($saleId),
            ],
        ];
    }
}
