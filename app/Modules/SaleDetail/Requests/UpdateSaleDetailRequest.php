<?php

namespace App\Modules\SaleDetail\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSaleDetailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sale_id' => ['sometimes', 'uuid', 'exists:sales,id'],
            'product_id' => ['sometimes', 'uuid', 'exists:products,id'],
            'qty' => ['sometimes', 'numeric', 'min:0'],
        ];
    }
}
