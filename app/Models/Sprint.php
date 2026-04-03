<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Sprint extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'id_sprint';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id_sprint',
        'id_proyecto',
        'id_creado_por',
        'id_estado',
        'titulo',
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
    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'id_proyecto');
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'id_creado_por');
    }

    public function estado()
    {
        return $this->belongsTo(EstadoSprint::class, 'id_estado');
    }

    public function tareas()
    {
        return $this->hasMany(Tarea::class, 'id_sprint');
    }
}
