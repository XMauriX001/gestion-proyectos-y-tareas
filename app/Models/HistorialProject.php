<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialProject extends Model
{
    use HasFactory;

    protected $table = 'historial_projects';
    protected $primaryKey = 'id_historial';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_proyecto',
        'id_cambiado_por',
        'accion',
        'detalles',
        'changed_at',
    ];

    protected $dates = [
        'changed_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'detalles' => 'json',
    ];

    // Relaciones
    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'id_proyecto');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_cambiado_por');
    }
}