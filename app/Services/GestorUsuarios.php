<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class GestorUsuarios
{
    public function Listar($pIncluyeBajas = 'N')
    {
        return DB::select('CALL bsp_listar_usuarios(?)', [$pIncluyeBajas]);
    }

    public function Alta($Username, $Apellidos, $Nombres, $FechaNacimiento, $Telefono, $Email, $Contrasena, $Rol)
    {
        return DB::select('CALL bsp_alta_usuario(?, ?, ?, ?, ?, ?, ?, ?)', [
            $Username,
            $Apellidos,
            $Nombres,
            $FechaNacimiento,
            $Telefono,
            $Email,
            $Contrasena,
            $Rol,
        ]);
    }

    public function Modifica($IdUsuario, $Username, $Apellidos, $Nombres, $FechaNacimiento, $Telefono, $Email, $Contrasena, $Rol)
    {
        return DB::select('CALL bsp_modifica_perfil(?, ?, ?, ?, ?, ?, ?, ?, ?)', [
            $IdUsuario,
            $Username,
            $Apellidos,
            $Nombres,
            $FechaNacimiento,
            $Telefono,
            $Email,
            $Contrasena,
            $Rol,
        ]);
    }

    public function Borra($IdUsuario)
    {
        return DB::select('CALL bsp_borra_usuario(?)', [$IdUsuario]);
    }

    public function DarBaja($IdUsuario)
    {
        return DB::select('CALL bsp_darbaja_usuario(?)', [$IdUsuario]);
    }

    public function Activar($IdUsuario)
    {
        return DB::select('CALL bsp_activar_usuario(?)', [$IdUsuario]);
    }
}