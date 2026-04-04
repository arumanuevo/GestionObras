@component('mail::message')
# Nueva Orden de Servicio Emitida

Hola **{{ $destinatario->name }}**,

El usuario **{{ $creador->name }}** ha emitido una nueva orden de servicio para ti:

@component('mail::panel')
**Tema:** {{ $ordenServicio->Tema }}<br>
**Número:** OS-{{ str_pad($ordenServicio->Nro, 4, '0', STR_PAD_LEFT) }}<br>
**Estado:** {{ $ordenServicio->Estado }}<br>
**Fecha:** {{ $ordenServicio->fecha ? $ordenServicio->fecha->format('d/m/Y') : 'No especificada' }}<br>
@if($ordenServicio->nota_pedido_id)
**En respuesta a:** Nota de Pedido NP-{{ str_pad($ordenServicio->nota_pedido_id, 4, '0', STR_PAD_LEFT) }}
@endif
@endcomponent

@if(!empty($ordenServicio->Observaciones))
**Observaciones:**
{{ $ordenServicio->Observaciones }}
@endif

@if($ordenServicio->resumen_ai)
**Resumen de IA del documento adjunto:**
{{ $ordenServicio->resumen_ai }}
@endif

@component('mail::button', ['url' => $url, 'color' => 'blue'])
Ver Orden de Servicio en el Sistema
@endcomponent

@if($ordenServicio->pdf_path)
**Documento adjunto:** [Descargar PDF]({{ asset('storage/' . $ordenServicio->pdf_path) }})
@endif

@component('mail::subcopy')
**Nota importante:** Este es un mensaje automático generado por el sistema. Por favor, no responda a este correo electrónico. Para cualquier consulta o acción relacionada con esta orden de servicio, utilice el sistema a través del enlace proporcionado.
@endcomponent

Gracias,<br>
{{ config('app.name') }}
@endcomponent