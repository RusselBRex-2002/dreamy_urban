<?php

namespace App\Http\Requests\Banner;

use App\Http\Requests\RequestWrapper;
use Illuminate\Validation\Rule;

class BannerRequest extends RequestWrapper
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
            'image.*' => isset($this->id) ? 'nullable|image|mimes:jpg,jpeg,png' : 'nullable|image|mimes:jpg,jpeg,png',
            'title' => ['required','max:255'],
            'background_image.*' => isset($this->id) ? 'nullable|image|mimes:jpg,jpeg,png' : 'nullable|image|mimes:jpg,jpeg,png',
        ];
    }
}
