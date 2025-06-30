<?php

namespace App\Services;

use App\Classes\Patrocinadores;
use Illuminate\Support\Facades\DB;

class GestorPatrocinadores
{
    public function Listar($IdEvento)
    {
        return DB::select('CALL bsp_listar_patrocinadores(?)', [$IdEvento]);
    }

    public function Buscar($IdEvento, $Patrocinador, $Offset, $Cantidad)
    {
        return DB::select('CALL bsp_buscar_patrocinadores(?,?,?,?)', [$IdEvento, $Patrocinador, $Offset, $Cantidad]);
    }

    public function Alta(Patrocinadores $patrocinador)
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

    public function Modifica(Patrocinadores $patrocinador)
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