<?php
// app/Mail/OrdenServicioCreada.php
namespace App\Mail;

use App\Models\OrdenServicio;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrdenServicioCreada extends Mailable
{
    use Queueable, SerializesModels;

    public $ordenServicio;
    public $creador;
    public $destinatario;

    public function __construct(OrdenServicio $ordenServicio, User $creador, User $destinatario)
    {
        $this->ordenServicio = $ordenServicio;
        $this->creador = $creador;
        $this->destinatario = $destinatario;
    }

    public function build()
    {
        return $this->subject('Nueva Orden de Servicio emitida: ' . $this->ordenServicio->Tema)
                    ->markdown('emails.orden_servicio_creada')
                    ->with([
                        'ordenServicio' => $this->ordenServicio,
                        'creador' => $this->creador,
                        'destinatario' => $this->destinatario,
                        'url' => route('obras.ordenes-servicio.show', [
                            'obra' => $this->ordenServicio->obra_id,
                            'orden_servicio' => $this->ordenServicio->id
                        ])
                    ]);
    }
}