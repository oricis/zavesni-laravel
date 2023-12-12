<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreArtistRequest extends FormRequest
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
            'name' => 'string|required|min:3|max:50'
        ];
    }

    public function messages()
    {
        return [
            'name.string' => 'Artist name must be string.',
            'name.required' => 'Artist name is required.',
            'name.min' => 'Minimum length of an artist name is 3 characters.',
            'name.max' => 'Artist name cannot exceed more than 50 characters.',
        ];
    }
}
