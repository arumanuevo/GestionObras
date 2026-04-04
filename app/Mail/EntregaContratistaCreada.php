<?php

// app/Mail/EntregaContratistaCreada.php
namespace App\Mail;

use App\Models\EntregaContratista;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EntregaContratistaCreada extends Mailable
{
    use Queueable, SerializesModels;

    public $entrega;
    public $creador;
    public $destinatario;

    public function __construct(EntregaContratista $entrega, User $creador, User $destinatario)
    {
        $this->entrega = $entrega;
        $this->creador = $creador;
        $this->destinatario = $destinatario;
    }

    public function build()
    {
        return $this->subject('Nueva entrega asignada: ' . $this->entrega->asunto)
                    ->markdown('emails.entrega_contratista_creada')
                    ->with([
                        'entrega' => $this->entrega,
                        'creador' => $this->creador,
                        'destinatario' => $this->destinatario,
                        'url' => route('obras.entregas-contratista.show', [
                            'obra' => $this->entrega->obra_id,
                            'entrega' => $this->entrega->id
                        ])
                    ]);
    }
}