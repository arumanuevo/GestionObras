@component('mail::message')
# Nueva Nota Asignada

Hola **{{ $destinatario->name }}**,

El usuario **{{ $creador->name }}** te ha asignado una nueva nota:

@component('mail::panel')
**Tema:** {{ $nota->Tema }}<br>
**Tipo:** {{ $nota->Tipo }}<br>
**Número:** {{ $nota->Nro }}<br>
**Estado:** {{ $nota->Estado }}<br>
**Fecha:** {{ $nota->fecha ? $nota->fecha->format('d/m/Y') : 'No especificada' }}
@endcomponent

@if(!empty($nota->Observaciones))
**Observaciones:**
{{ $nota->Observaciones }}
@endif

@if($nota->link)
Puedes ver más detalles [aquí]({{ $nota->link }}).
@endif

@component('mail::button', ['url' => $url, 'color' => 'blue'])
Ver Nota en el Sistema
@endcomponent

@if($nota->pdf_path)
**Documento adjunto:** [Descargar PDF]({{ asset('storage/' . $nota->pdf_path) }})
@endif

Gracias,<br>
{{ config('app.name') }}
@endcomponent
