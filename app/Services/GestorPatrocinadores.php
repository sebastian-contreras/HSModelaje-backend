<?php

namespace App\Services;

use App\Classes\Patrocinadores;
use Illuminate\Support\Facades\DB;

class GestorPatrocinadores extends GestorBaseEvento
{
    public function Listar($IdEvento)
    {
        return DB::select('CALL bsp_listar_patrocinadores(?)', [$IdEvento]);
    }

    public function Buscar($Offset, $Cantidad, $IdEvento, $Patrocinador = null)
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
}