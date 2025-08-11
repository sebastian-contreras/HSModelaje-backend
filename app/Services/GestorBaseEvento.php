<?php
namespace App\Services;

abstract class GestorBaseEvento
{
    abstract public function Listar($idEvento);
    abstract public function Buscar($offset, $cantidad,$idEvento);
    abstract public function Alta($entidad);
    abstract public function Modifica($entidad);
    abstract public function Borra($id);
    
}
