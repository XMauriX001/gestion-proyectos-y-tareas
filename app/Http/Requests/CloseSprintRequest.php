<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CloseSprintRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('product_owner');
    }

    public function rules(): array
    {
        return [
            'comentario' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'comentario.max' => 'El comentario no puede exceder 500 caracteres',
        ];
    }
}