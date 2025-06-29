<?php

namespace App\Classes;

use DB;

class Usuarios
{
    public $IdUsuario;
    public $Username;
    public $Apellidos;
    public $Nombres;
    public $FechaNacimiento;
    public $Telefono;
    public $Email;
    public $Contrasena;
    public $Rol;
    public $EstadoUsuario;

    public function __construct(array $data)
    {
        $this->IdUsuario = $data['IdUsuario'] ?? null;
        $this->Username = $data['Username'] ?? null;
        $this->Apellidos = $data['Apellidos'] ?? null;
        $this->Nombres = $data['Nombres'] ?? null;
        $this->FechaNacimiento = $data['FechaNacimiento'] ?? null;
        $this->Telefono = $data['Telefono'] ?? null;
        $this->Email = $data['Email'] ?? null;
        $this->Contrasena = $data['Contrasena'] ?? null;
        $this->Rol = $data['Rol'] ?? null;
        $this->EstadoUsuario = $data['EstadoUsuario'] ?? null;
    }


    public function Dame()
    {
        return DB::select('CALL bsp_dame_usuario(?)', [$this->IdUsuario]);
    }

    public function Activar()
    {
        return DB::select('CALL bsp_activar_usuario(?)', [$this->IdUsuario]);
    }

    public function DarBaja()
    {
        return DB::select('CALL bsp_darbaja_usuario(?)', [$this->IdUsuario]);
    }
}