<?php

namespace App\Mail;

use App\Models\Nota;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotaCreada extends Mailable
{
    use Queueable, SerializesModels;

    public $nota;
    public $creador;
    public $destinatario;

    public function __construct(Nota $nota, User $creador, User $destinatario)
    {
        $this->nota = $nota;
        $this->creador = $creador;
        $this->destinatario = $destinatario;
    }

    public function build()
    {
        return $this->subject('Nueva nota asignada: ' . $this->nota->Tema)
                    ->markdown('emails.nota_creada')
                    ->with([
                        'nota' => $this->nota,
                        'creador' => $this->creador,
                        'destinatario' => $this->destinatario,
                        'url' => url('/notas/' . $this->nota->id)
                    ]);
    }
}
