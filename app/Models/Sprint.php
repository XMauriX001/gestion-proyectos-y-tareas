<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sprint extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_sprint';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
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