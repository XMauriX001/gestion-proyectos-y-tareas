<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class HistorialProject extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'historial_projects';
    protected $primaryKey = 'id_historial';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id_historial',
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
