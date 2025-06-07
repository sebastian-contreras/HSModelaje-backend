<?php

namespace App\Events;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class VotoModeloIniciado implements ShouldBroadcast
{
    use InteractsWithSockets;

    public $participante;
    public $accion; //iniciar o  detener

    public function __construct($participante,$accion)
    {
        $this->participante = $participante;
        $this->accion = $accion;
    }

    public function broadcastOn()
    {
        return new Channel('evento-'.$this->participante->IdEvento);
    }
}
