<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:255|unique:items,sku',
            'barcode' => 'nullable|string|max:255|unique:items,barcode',
            'description' => 'nullable|string|max:2000',
            'price' => 'required|numeric|min:0',
            'initial_stock' => 'nullable|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Please select a category.',
            'category_id.exists' => 'The selected category does not exist.',
            'price.min' => 'Price must be a positive number.',
        ];
    }
}
