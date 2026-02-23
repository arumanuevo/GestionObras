<?php

namespace App\Notifications;

use App\Models\Nota;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;

class NotaAsignadaNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $nota;
    protected $creador;

    public function __construct(Nota $nota, User $creador)
    {
        $this->nota = $nota;
        $this->creador = $creador;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = url('/notas/' . $this->nota->id);

        // Parsear la fecha
        $fechaFormateada = 'No especificada';
        if ($this->nota->fecha) {
            try {
                $fechaFormateada = Carbon::parse($this->nota->fecha)->format('d/m/Y');
            } catch (\Exception $e) {
                $fechaFormateada = $this->nota->fecha;
            }
        }

        $mail = (new MailMessage)
            ->subject('Nueva nota asignada: ' . $this->nota->Tema)
            ->greeting('Hola ' . $notifiable->name)
            ->line('El usuario **' . $this->creador->name . '** te ha asignado una nueva nota.')
            ->line('**Tema:** ' . $this->nota->Tema)
            ->line('**Tipo:** ' . $this->nota->Tipo)
            ->line('**Número:** ' . $this->nota->Nro)
            ->line('**Estado:** ' . $this->nota->Estado)
            ->line('**Fecha:** ' . $fechaFormateada);

        // Agregar observaciones solo si existen
        if (!empty($this->nota->Observaciones)) {
            $mail->line('**Observaciones:**')
                 ->line($this->nota->Observaciones);
        }

        // Agregar link si existe (sin mostrar URL completa)
        if ($this->nota->link) {
            $mail->line('Puedes ver más detalles (' . $this->nota->link . ').');
        }

        $mail->action('Ver Nota en el Sistema', $url);

        // Agregar PDF si existe
        if ($this->nota->pdf_path) {
            $mail->line('**Documento adjunto:** [Descargar PDF](' . asset('storage/' . $this->nota->pdf_path) . ')');
        }

        $mail->line('Gracias por usar nuestra aplicación!');

        return $mail;
    }

    public function toArray($notifiable)
    {
        return [
            'nota_id' => $this->nota->id,
            'creador_id' => $this->creador->id,
            'message' => 'Nueva nota asignada: ' . $this->nota->Tema
        ];
    }
}


