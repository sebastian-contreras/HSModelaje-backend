<?php

namespace App\Services;

use App\Classes\Patrocinadores;
use Illuminate\Support\Facades\DB;

class GestorPatrocinadores extends GestorBase
{
    public function ListarEnEvento($IdEvento)
    {
        return DB::select('CALL bsp_listar_patrocinadores(?)', [$IdEvento]);
    }

    public function BuscarEnEvento($Offset, $Cantidad, $IdEvento, $Patrocinador = null)
    {
        return DB::select('CALL bsp_buscar_patrocinadores(?,?,?,?)', [$IdEvento, $Patrocinador, $Offset, $Cantidad]);
    }

    public function Alta($patrocinador)
    {
        return DB::select('CALL bsp_alta_patrocinador(?, ?, ?, ?, ?, ?)', [
            $patrocinador->IdEvento,
            $patrocinador->Patrocinador,
            $patrocinador->Correo,
            $patrocinador->Telefono,
            $patrocinador->DomicilioRef,
            $patrocinador->Descripcion,
        ]);
    }

    public function Modifica($patrocinador)
    {
        return DB::select('CALL bsp_modifica_patrocinador(?, ?, ?, ?, ?, ?)', [
            $patrocinador->IdPatrocinador,
            $patrocinador->Patrocinador,
            $patrocinador->Correo,
            $patrocinador->Telefono,
            $patrocinador->DomicilioRef,
            $patrocinador->Descripcion,
        ]);
    }

    public function Borra($IdPatrocinador)
    {
        return DB::select('CALL bsp_borra_patrocinador(?)', [$IdPatrocinador]);
    }
    /**
     * @deprecated No implementado.
     */
    public function Listar($IdEntidad)
    {
        trigger_error(
            'El método ' . __METHOD__ . ' está deprecado y no hace nada.',
            E_USER_DEPRECATED
        );
    }
    /**
     * @deprecated No implementado.
     */
    public function Buscar($offset, $cantidad)
    {
        trigger_error(
            'El método ' . __METHOD__ . ' está deprecado y no hace nada.',
            E_USER_DEPRECATED
        );
    }
}