<?php

namespace App\Http\Requests\Coupon;

use App\Http\Requests\RequestWrapper;
use Illuminate\Validation\Rule;

class CouponRequest extends RequestWrapper
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
            'name' => ['required','max:255'],
            'type' => ['required'],
            'discount' => ['required'],
            'expiry_date' => ['required'],
        ];
    }
}
