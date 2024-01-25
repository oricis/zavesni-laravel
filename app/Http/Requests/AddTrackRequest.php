<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddTrackRequest extends FormRequest
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
            'title' => 'required|string',
            'owner' => 'required|uuid',
            'cover' => 'required',
            'track' => 'required|mimes:mp3,wav',
            'genre' => 'required|uuid',
            'album' => 'nullable|sometimes|uuid',
            'explicit' => 'nullable'
        ];
    }
}
