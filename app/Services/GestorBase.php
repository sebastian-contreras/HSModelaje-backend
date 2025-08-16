<?php
namespace App\Services;
abstract class GestorBase
{
    abstract public function Listar($incluyeBajas);
    abstract public function Buscar($offset, $cantidad);
    abstract public function Alta($entidad);
    abstract public function Modifica($entidad);
    abstract public function Borra($id);
    abstract public function ListarEnEvento($idEvento);
    abstract public function BuscarEnEvento($offset, $cantidad, $idEvento);
}
