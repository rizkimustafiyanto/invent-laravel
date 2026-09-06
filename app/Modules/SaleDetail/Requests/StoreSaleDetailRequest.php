<?php

namespace App\Modules\SaleDetail\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSaleDetailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sale_id' => ['required', 'uuid', 'exists:sales,id'],
            'product_id' => ['required', 'uuid', 'exists:products,id'],
            'qty' => ['required', 'numeric', 'min:0'],
        ];
    }
}
