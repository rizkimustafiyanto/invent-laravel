<?php

namespace App\Modules\Product\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product')?->id ?? $this->route('product');

        return [
            'code' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('products', 'code')->ignore($productId),
            ],
            'name' => ['sometimes', 'string', 'max:255'],
            'price' => ['sometimes', 'numeric', 'min:1'],
            'stock' => ['sometimes', 'numeric', 'min:0'],
            'category_id' => ['sometimes', 'nullable', 'uuid', 'exists:categories,id'],
            'image' => ['nullable', 'image', 'max:2048'],
        ];
    }
}
