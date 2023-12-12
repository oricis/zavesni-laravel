<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePlaylistRequest extends FormRequest
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
            'title' => 'required|min:5|max:50|string',
            'description' => 'nullable|max:150|string',
            //'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|dimensions:min_width=100,min_height=100,max_width=1000,max_height=1000',
        ];
    }
    public function messages()
    {
        return [
            'title.required' => 'Title field is required.',
            'title.min' => 'Title minimum length is 5 characters.',
            'title.max' => 'Title cannot exceed more than 50 characters.',
            'title.string' => 'Title field content must be string.',
            'description.max' => 'Description max length is 150 characters.',
            'description.string' => 'Description must be string.',
            //'image' => ''
        ];
    }
}
