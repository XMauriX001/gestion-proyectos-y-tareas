<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoProyecto extends Model
{
    use HasFactory;

    protected $table = 'estado_proyectos';
    protected $primaryKey = 'id_estado';
    public $timestamps = true;

    protected $fillable = [
        'estado',
    ];

    // Relaciones
    public function proyectos()
    {
        return $this->hasMany(Proyecto::class, 'id_estado');
    }
}
