<?php

namespace App\Events;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ListadoVotosParticipantes implements ShouldBroadcast
{
    use InteractsWithSockets;

    public $votos;
    public $IdEvento;

    public function __construct($votos,$IdEvento)
    {
        $this->votos = $votos;
        $this->IdEvento = $IdEvento;

    }

    public function broadcastOn()
    {
        return new Channel('evento-'.$this->IdEvento);
    }
}
