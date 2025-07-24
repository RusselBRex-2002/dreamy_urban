<?php

namespace App\Http\Requests\Product;

use App\Http\Requests\RequestWrapper;
use Illuminate\Validation\Rule;

class ProductRequest extends RequestWrapper
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required'],
            'category' => ['required'],
            'banner_image.*' => isset($this->id) ? 'nullable|image|mimes:jpg,jpeg,png' : 'nullable|image|mimes:jpg,jpeg,png',
            'price' => ['required'],
            'sku' => ['required'],
            'no_of_qty' => ['required'],
            'description' => ['required'],
        ];
    }
}
