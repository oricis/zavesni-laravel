<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'string|required|min:3|max:30',
            'last_name' => 'string|required|min:3|max:30',
            'email' => 'required|email|unique:actors,email',
            'password' => 'required|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/'
        ];
    }

    public function messages()
    {
        return [
            'first_name.string' => 'First name must be string.',
            'first_name.required' => 'First name is required.',
            'first_name.min' => 'First name minimum length is 3 characters.',
            'first_name.max' => 'First name maximum length is 30 characters.',
            'last_name.string' => 'Last name must be string.',
            'last_name.required' => 'Last name is required.',
            'last_name.min' => 'Last name minimum length is 3 characters.',
            'last_name.max' => 'Last name maximum length is 30 characters.',
            'email.required' => 'Email is required.',
            'email.email' => 'Email is in wrong format.',
            'email.unique' => 'Another user is using provided email.',
            'password.min' => 'Password must contain at least 8 characters.',
            'password.required' => 'Password is required.',
            'password.regex' => 'Password must contain at least one lowercase letter, one uppercase letter, one number and one symbol.'
        ];
    }
}
