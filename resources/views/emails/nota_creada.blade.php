@component('mail::message')
# Nueva Nota Asignada

Hola **{{ $destinatario->name }}**,

El usuario **{{ $creador->name }}** te ha asignado una nueva nota:

@component('mail::panel')
**Tema:** {{ $nota->Tema }}<br>
**Tipo:** {{ $nota->Tipo }}<br>
**Número:** {{ $nota->Tipo }}-{{ str_pad($nota->Nro, 4, '0', STR_PAD_LEFT) }}<br>
**Estado:** {{ $nota->Estado }}<br>
**Fecha:** {{ $nota->fecha ? $nota->fecha->format('d/m/Y') : 'No especificada' }}
@endcomponent

@if(!empty($nota->Observaciones))
**Observaciones:**
{{ $nota->Observaciones }}
@endif

@if($nota->resumen_ai)
**Resumen de IA del documento adjunto:**
{{ $nota->resumen_ai }}
@endif

@component('mail::button', ['url' => $url, 'color' => 'blue'])
Ver Nota en el Sistema
@endcomponent

@if($nota->pdf_path)
**Documento adjunto:** [Descargar PDF]({{ asset('storage/' . $nota->pdf_path) }})
@endif

@component('mail::subcopy')
**Nota importante:** Este es un mensaje automático generado por el sistema. Por favor, no responda a este correo electrónico. Para cualquier consulta o acción relacionada con esta nota, utilice el sistema a través del enlace proporcionado.
@endcomponent

Gracias,<br>
{{ config('app.name') }}
@endcomponent