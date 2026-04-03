<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrioridadTarea extends Model
{
    use HasFactory;

    protected $table = 'prioridad_tareas';
    protected $primaryKey = 'id_prioridad';
    public $timestamps = true;

    protected $fillable = [
        'prioridad',
    ];

    // Relaciones
    public function tareas()
    {
        return $this->hasMany(Tarea::class, 'id_prioridad');
    }
}
