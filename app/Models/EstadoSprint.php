<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoSprint extends Model
{
    use HasFactory;

    protected $table = 'estado_sprints';
    protected $primaryKey = 'id_estado';
    public $timestamps = true;

    protected $fillable = [
        'estado',
    ];

    // Relaciones
    public function sprints()
    {
        return $this->hasMany(Sprint::class, 'id_estado');
    }
}
