<?php

namespace App\Classes;

use DB;

class Entradas
{
    public $IdEntrada;
    public $IdZona;
    public $ApelName;
    public $DNI;
    public $Correo;
    public $Telefono;
    public $Comprobante;
    public $Cantidad;
    public $Token;

    public function __construct(array $data)
    {
        $this->IdEntrada = $data['IdEntrada'] ?? null;
        $this->IdZona = $data['IdZona'] ?? null;
        $this->ApelName = $data['ApelName'] ?? null;
        $this->DNI = $data['DNI'] ?? null;
        $this->Correo = $data['Correo'] ?? null;
        $this->Telefono = $data['Telefono'] ?? null;
        $this->Comprobante = $data['Comprobante'] ?? null;
        $this->Cantidad = $data['Cantidad'] ?? null;
        $this->Token = $data['Token'] ?? null;
    }

    public function Dame()
    {
        return DB::select('CALL bsp_dame_entrada(?)', [$this->IdEntrada]);
    }

    public function DamePorToken()
    {
        return DB::select('CALL bsp_dame_entrada_token(?)', [$this->Token]);
    }
       public function Abonar($IdEntrada)
    {
        return DB::select('CALL bsp_abonar_entrada(?)', [$IdEntrada]);
    }

    public function Usar($IdEntrada)
    {
        return DB::select('CALL bsp_usar_entrada(?)', [$IdEntrada]);
    }

    public function Rechazar($IdEntrada)
    {
        return DB::select('CALL bsp_rechazar_entrada(?)', [$IdEntrada]);
    }
}