@component('mail::message')
# Nueva Nota para el Equipo de Proyecto

Hola **{{ $destinatario->name }}**,

El usuario **{{ $creador->name }}** te ha asignado una nueva nota para el equipo de proyecto:

@component('mail::panel')
**Tema:** {{ $nota->tema }}<br>
**Número:** {{ $nota->numero }}<br>
**Fecha:** {{ $nota->fecha ? $nota->fecha->format('d/m/Y') : 'No especificada' }}<br>
**Tipo de entrega:** {{ $nota->tipo_entrega }}<br>
**Plazo de entrega:** {{ $nota->plazo_entrega }} días<br>
**Prioridad:** {{ $nota->prioridad }}
@endcomponent

@if(!empty($nota->contenido))
**Contenido:**
{{ $nota->contenido }}
@endif

@component('mail::button', ['url' => $url, 'color' => 'blue'])
Ver Nota en el Sistema
@endcomponent

@if($nota->archivos->count() > 0)
**Archivos adjuntos:**
@foreach($nota->archivos as $archivo)
- [{{ $archivo->nombre_original }}]({{ asset('storage/' . $archivo->ruta) }})
@endforeach
@endif

@component('mail::subcopy')
**Nota importante:** Este es un mensaje automático generado por el sistema. Por favor, no responda a este correo electrónico. Para cualquier consulta o acción relacionada con esta nota, utilice el sistema a través del enlace proporcionado.
@endcomponent

Gracias,<br>
{{ config('app.name') }}
@endcomponent