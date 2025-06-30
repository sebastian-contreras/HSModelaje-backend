<?php

namespace App\Classes;

use DB;

class Patrocinadores
{
    public $IdPatrocinador;
    public $IdEvento;
    public $Patrocinador;
    public $Correo;
    public $Telefono;
    public $DomicilioRef;
    public $Descripcion;

    public function __construct(array $data)
    {
        $this->IdPatrocinador = $data['IdPatrocinador'] ?? null;
        $this->IdEvento = $data['IdEvento'] ?? null;
        $this->Patrocinador = $data['Patrocinador'] ?? null;
        $this->Correo = $data['Correo'] ?? null;
        $this->Telefono = $data['Telefono'] ?? null;
        $this->DomicilioRef = $data['DomicilioRef'] ?? null;
        $this->Descripcion = $data['Descripcion'] ?? null;
    }

    public function Dame()
    {
        return DB::select('CALL bsp_dame_patrocinador(?)', [$this->IdPatrocinador]);
    }


}