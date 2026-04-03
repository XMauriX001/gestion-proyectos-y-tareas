<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Tarea extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'id_tareas';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id_tareas',
        'id_proyecto',
        'id_creado_por',
        'id_asignado_a',
        'id_sprint',
        'id_estado',
        'id_prioridad',
        'titulo',
        'descripcion',
        'fecha_entrega',
    ];

    protected $dates = [
        'fecha_entrega',
        'created_at',
        'updated_at',
    ];

    // Relaciones
    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'id_proyecto');
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'id_creado_por');
    }

    public function asignado()
    {
        return $this->belongsTo(User::class, 'id_asignado_a');
    }

    public function sprint()
    {
        return $this->belongsTo(Sprint::class, 'id_sprint');
    }

    public function estado()
    {
        return $this->belongsTo(EstadoTarea::class, 'id_estado');
    }

    public function prioridad()
    {
        return $this->belongsTo(PrioridadTarea::class, 'id_prioridad');
    }
}
