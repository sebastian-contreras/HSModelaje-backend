<?php

namespace App\Services;

use App\Classes\Usuario;
use Illuminate\Support\Facades\DB;

class GestorUsuarios
{
    public function Listar($pIncluyeBajas = 'N')
    {
        return DB::select('CALL bsp_listar_usuarios(?)', [$pIncluyeBajas]);
    }

   public function Alta(Usuario $usuario)
    {
        return DB::select('CALL bsp_alta_usuario(?, ?, ?, ?, ?, ?, ?, ?)', [
            $usuario->Username,
            $usuario->Apellidos,
            $usuario->Nombres,
            $usuario->FechaNacimiento,
            $usuario->Telefono,
            $usuario->Email,
            $usuario->Contrasena,
            $usuario->Rol,
        ]);
    }

    public function Modifica(Usuario $usuario)
    {
        return DB::select('CALL bsp_modifica_perfil(?, ?, ?, ?, ?, ?, ?, ?, ?)', [
            $usuario->IdUsuario,
            $usuario->Username,
            $usuario->Apellidos,
            $usuario->Nombres,
            $usuario->FechaNacimiento,
            $usuario->Telefono,
            $usuario->Email,
            $usuario->Contrasena,
            $usuario->Rol,
        ]);
    }

    public function Borra($IdUsuario)
    {
        return DB::select('CALL bsp_borra_usuario(?)', [$IdUsuario]);
    }

    public function ModificaRol($IdUsuario, $Rol)
    {
        return DB::select('CALL bsp_activar_usuario(?,?)', [$IdUsuario, $Rol]);
    }
}