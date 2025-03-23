<?php

namespace App\Mail;

use DB;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EntradaAprobadaMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $modelo;

    public $data;

    public function __construct($entrada)
    {
        $zona = DB::select('CALL bsp_dame_zona(?)', [$entrada[0]->IdZona]);
        $evento = DB::select('CALL bsp_dame_evento(?)', [$entrada[0]->IdEvento]);
        $establecimiento = DB::select('CALL bsp_dame_establecimiento(?)', [$entrada[0]->IdEstablecimiento]);

        $this->data = [
            'entrada' => $entrada[0],
            'zona' => $zona[0],
            'evento' => $evento[0],
            'establecimiento' => $establecimiento[0],
        ];
    }
    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🎟️ ¡Tu entrada ha sido aprobada! | Detalles dentro 🎉',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mails.EntradaAprobadaMail',
            with: $this->data, // Pasamos la data a la vista
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
