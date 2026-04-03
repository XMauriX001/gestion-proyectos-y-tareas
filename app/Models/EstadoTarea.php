<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoTarea extends Model
{
    use HasFactory;

    protected $table = 'estado_tareas';
    protected $primaryKey = 'id_estado';
    public $timestamps = true;

    protected $fillable = [
        'estado',
    ];

    // Relaciones
    public function tareas()
    {
        return $this->hasMany(Tarea::class, 'id_estado');
    }
}
