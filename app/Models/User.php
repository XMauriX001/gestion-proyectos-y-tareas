<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relaciones
    public function proyectos()
    {
        return $this->hasMany(Proyecto::class, 'creado_por');
    }

    public function sprints()
    {
        return $this->hasMany(Sprint::class, 'id_creado_por');
    }

    public function tareasAsignadas()
    {
        return $this->hasMany(Tarea::class, 'id_asignado_a');
    }

    public function tareasCreadas()
    {
        return $this->hasMany(Tarea::class, 'id_creado_por');
    }

    public function historialProjects()
    {
        return $this->hasMany(HistorialProject::class, 'id_cambiado_por');
    }
}