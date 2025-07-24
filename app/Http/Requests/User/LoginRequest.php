<?php

namespace App\Http\Requests\User;

use App\Http\Requests\RequestWrapper;
use Illuminate\Validation\Rule;

class LoginRequest extends RequestWrapper
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
            'email' => ['required', 'email', 'exists:users'],
            'password' => ['required'],
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'Email is required',
            'email.email' => 'Invalid Email',
            'email.exists' => 'Email is not registered',
            'password.required' => 'Password is required',
        ];
    }

    public function filters()
    {
        return [
            'email' => 'trim'
        ];
    }
}
