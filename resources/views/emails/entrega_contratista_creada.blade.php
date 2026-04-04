@component('mail::message')
# Nueva Entrega Asignada

Hola **{{ $destinatario->name }}**,

El usuario **{{ $creador->name }}** te ha asignado una nueva entrega:

@component('mail::panel')
**Asunto:** {{ $entrega->asunto }}<br>
**Número:** {{ $entrega->numero }}<br>
**Fecha:** {{ $entrega->fecha ? $entrega->fecha->format('d/m/Y') : 'No especificada' }}<br>
**Tipo de entrega:** {{ $entrega->tipo_entrega }}<br>
**Plazo de entrega:** {{ $entrega->plazo_recepcion }} días<br>
**Prioridad:** {{ $entrega->prioridad }}
@endcomponent

@if(!empty($entrega->descripcion))
**Descripción:**
{{ $entrega->descripcion }}
@endif

@component('mail::button', ['url' => $url, 'color' => 'blue'])
Ver Entrega en el Sistema
@endcomponent

@if($entrega->archivos->count() > 0)
**Archivos adjuntos:**
@foreach($entrega->archivos as $archivo)
- [{{ $archivo->nombre_original }}]({{ asset('storage/' . $archivo->ruta) }})
@endforeach
@endif

@component('mail::subcopy')
**Nota importante:** Este es un mensaje automático generado por el sistema. Por favor, no responda a este correo electrónico. Para cualquier consulta o acción relacionada con esta entrega, utilice el sistema a través del enlace proporcionado.
@endcomponent

Gracias,<br>
{{ config('app.name') }}
@endcomponent