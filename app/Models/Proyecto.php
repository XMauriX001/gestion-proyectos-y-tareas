<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proyecto extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_proyecto';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
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