<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable; 

    protected $table = 'Usuarios'; // nombre exacto de tu tabla

    protected $primaryKey = 'IdUsuario';

    public $incrementing = true;

    public $timestamps = false; // si no usás created_at y updated_at

    protected $fillable = [
        'Username',
        'Apellidos',
        'Nombres',
        'FechaNacimiento',
        'Telefono',
        'Email',
        'Contrasena',
        'FechaCreado',
        'Rol',
        'EstadoUsuario'
    ];

    protected $hidden = [
        'Contrasena',
    ];

    // Laravel espera un campo "password", lo aliasamos
    public function getAuthPassword()
    {
        return $this->Contrasena;
    }

    public function getAuthIdentifierName()
    {
        return 'IdUsuario';
    }
}
