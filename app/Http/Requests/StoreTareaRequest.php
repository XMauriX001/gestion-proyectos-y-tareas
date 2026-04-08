<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTareaRequest extends FormRequest
{
    public function authorize(): bool
    {
        $proyectoId = $this->route('id');
        $proyecto = \App\Models\Proyecto::find($proyectoId);
        
        if (!$proyecto) {
            return false;
        }

        return $proyecto->creado_por === $this->user()->id || $this->user()->can('crear_tarea');
    }

    public function rules(): array
    {
        return [
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'fecha_entrega' => 'required|date',
            'id_prioridad' => 'nullable|integer',
            'id_sprint' => 'nullable|exists:sprints,id_sprint',
        ];
    }

    public function messages(): array 
    {
        return [
            'titulo.unique' => 'Ya existe una tarea con este nombre en el proyecto',
            'fecha_entrega.date' => 'La fecha de entrega debe ser una fecha valida',       
        ];
    }
}
