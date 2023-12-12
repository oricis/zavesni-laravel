<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAlbumRequest extends FormRequest
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
            'name' => 'string|required|max:50',
            'artist' => 'uuid'
        ];
    }

    public function messages()
    {
        return [
            'name.string' => 'Album name must be string.',
            'name.required' => 'Album name is required.',
            'name.max' => 'Max character length is 50 characters.',
            'artist.uuid' => 'Artist must be a valid UUID.'
        ];
    }
}
