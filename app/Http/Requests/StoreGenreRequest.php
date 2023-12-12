<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGenreRequest extends FormRequest
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
            'name' => 'required|string|min:3|max:50|unique:genres'
        ];
    }
    public function messages() : array
    {
        return [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name field must be string.',
            'name.min' => 'The name field must have at least 3 characters.',
            'name.max' => 'The name field cannot exceed 50 characters.',
            'name.unique' => 'The name field must be unique.'
        ];
    }
}
