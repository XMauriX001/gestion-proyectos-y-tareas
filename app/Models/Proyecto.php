<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Proyecto extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'id_proyecto';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id_proyecto',
        'creado_por',
        'id_estado',
        'titulo',
        'descripcion',
        'fecha_inicio',
        'fecha_final',
    ];

    protected $dates = [
        'fecha_inicio',
        'fecha_final',
        'created_at',
        'updated_at',
    ];

    // Relaciones
    public function creador()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function estado()
    {
        return $this->belongsTo(EstadoProyecto::class, 'id_estado');
    }

    public function sprints()
    {
        return $this->hasMany(Sprint::class, 'id_proyecto');
    }

    public function tareas()
    {
        return $this->hasMany(Tarea::class, 'id_proyecto');
    }

    public function historial()
    {
        return $this->hasMany(HistorialProject::class, 'id_proyecto');
    }
}
