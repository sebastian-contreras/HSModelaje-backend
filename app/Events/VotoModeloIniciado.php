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

    public function __construct($participante)
    {
        $this->participante = $participante;
    }

    public function broadcastOn()
    {
        return new Channel('evento-'.$this->participante->IdEvento);
    }
}
