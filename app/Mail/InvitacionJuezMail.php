<?php

namespace App\Mail;

use DB;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvitacionJuezMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $data;

    public function __construct($juez)
    {
        $data = DB::select('CALL bsp_dame_juez(?)', [$juez[0]->IdJuez]);
        $evento = DB::select('CALL bsp_dame_evento(?)', [$juez[0]->IdEvento]);
        $establecimiento = DB::select('CALL bsp_dame_establecimiento(?)', [$evento[0]->IdEstablecimiento]);

        $this->data = [
            'juez' => $data[0],
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
            subject: 'Invitación como Jurado para el evento ' . $this->data['evento']->Evento . ' 🎉',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mails.InvitacionJuezMail',
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
