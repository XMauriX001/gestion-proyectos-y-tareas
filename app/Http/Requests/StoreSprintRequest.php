<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSprintRequest extends FormRequest
{
    public function authorize(): bool
    {
        $projectId = $this->route('id');

        $proyecto = \App\Models\Proyecto::findOrFail($projectId);

        return $proyecto->creado_por == $this->user()->id || $this->user()->can('editar_proyecto');
    }

    public function rules(): array
    {
        return [
            'titulo' => 'required|string|max:255',
            'fecha_inicio' => 'required|date|after_or_equal:today',
            'fecha_final' => 'required|date|after_or_equal:fecha_inicio',
        ];
    }

    public function messages(): array
    {
        return [
            'titulo.required' => 'El título del sprint es obligatorio',
            'fecha_inicio.after_or_equal' => 'La fecha de inicio debe ser hoy o posterior',
            'fecha_final.after_or_equal' => 'La fecha final debe ser igual o posterior a la de inicio',
        ];
    }
}