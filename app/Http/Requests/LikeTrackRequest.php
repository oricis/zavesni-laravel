<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class LikeTrackRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'track' => 'uuid|required'
        ];
    }
    public function messages(): array
    {
        return [
            'track.uuid' => 'Track must be a valid UUID.',
            'track.required' => 'Track is required.'
        ];
    }
}
