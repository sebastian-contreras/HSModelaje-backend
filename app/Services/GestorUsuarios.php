<?php

namespace App\Services;

use App\Classes\Usuarios;
use Illuminate\Support\Facades\DB;

class GestorUsuarios extends GestorBase
{
    public function Listar($pIncluyeBajas = 'N')
    {
        return DB::select('CALL bsp_listar_usuarios(?)', [$pIncluyeBajas]);
    }


    public function Buscar($Offset, $Cantidad, $Cadena = null, $Nombre = null, $Apellido = null, $Rol = null, $IncluyeInactivos = 'N')
    {
        return DB::select('CALL bsp_buscar_usuario(?,?,?,?,?,?,?)', [$Cadena, $Nombre, $Apellido, $Rol, $IncluyeInactivos, $Offset, $Cantidad]);
    }


    public function Alta($usuario)
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

    public function Modifica($usuario)
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
    /**
     * @deprecated No implementado.
     */
    public function ListarEnEvento($IdEntidad)
    {
        trigger_error(
            'El método ' . __METHOD__ . ' está deprecado y no hace nada.',
            E_USER_DEPRECATED
        );
    }
    /**
     * @deprecated No implementado.
     */
    public function BuscarEnEvento($offset, $cantidad, $idEvento)
    {
        trigger_error(
            'El método ' . __METHOD__ . ' está deprecado y no hace nada.',
            E_USER_DEPRECATED
        );
    }
}