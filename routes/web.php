<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ObraController;
use App\Http\Controllers\OrdenServicioController;
use App\Http\Controllers\NotaPedidoController;
use App\Http\Controllers\LibroObraController;
use App\Http\Controllers\NotaEquipoProyectoController;
use App\Http\Controllers\EntregaContratistaController;

// Ruta principal
Route::get('/', function () {
    return view('welcome');
});

// Rutas de autenticación
require __DIR__.'/auth.php';

// Ruta para usuarios no aprobados
Route::get('/unapproved', [LoginController::class, 'showUnapprovedMessage'])->name('unapproved');

// Rutas de verificación de email (fuera del middleware auth)
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
    ->middleware(['signed'])
    ->name('verification.verify');

Route::post('/email/resend', [VerificationController::class, 'resend'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.resend');

Route::get('/email/verify', [VerificationController::class, 'show'])
    ->middleware(['auth'])
    ->name('verification.notice');

// Rutas de perfil de usuario
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

// Rutas que requieren autenticación y aprobación
Route::middleware(['auth', 'approved'])->group(function () {
    //Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/home', [ObraController::class, 'index'])->name('home');

    // Rutas de gestión de usuarios
    Route::resource('users', UserController::class);
    Route::patch('/users/{user}/approve', [UserController::class, 'approve'])->name('users.approve');

    // Rutas para Obras
    Route::resource('obras', ObraController::class);
    
    // Rutas para Notas de Pedido y Órdenes de Servicio dentro de Obras
    Route::prefix('obras/{obra}')->group(function () {
        // =============================================
        // RUTAS PARA ÓRDENES DE SERVICIO
        // =============================================
        //Route::get('/ordenes-servicio/bandeja', [OrdenServicioController::class, 'bandejaOrdenes'])->name('obras.ordenes.bandeja');
        Route::get('/ordenes-servicio/bandeja', [OrdenServicioController::class, 'bandejaOrdenes'])->name('obras.ordenes-servicio.bandeja');
        Route::get('/ordenes-servicio', [OrdenServicioController::class, 'index'])->name('obras.ordenes-servicio.index');
        Route::get('/ordenes-servicio/crear', [OrdenServicioController::class, 'create'])->name('obras.ordenes-servicio.create');
        Route::post('/ordenes-servicio', [OrdenServicioController::class, 'store'])->name('obras.ordenes-servicio.store');
        
        Route::get('/ordenes-servicio/{orden_servicio}', [OrdenServicioController::class, 'show'])->name('obras.ordenes-servicio.show');
        Route::get('/ordenes-servicio/{orden_servicio}/editar', [OrdenServicioController::class, 'edit'])->name('obras.ordenes-servicio.edit');
        Route::put('/ordenes-servicio/{orden_servicio}', [OrdenServicioController::class, 'update'])->name('obras.ordenes-servicio.update');
        Route::post('/ordenes-servicio/{orden_servicio}/firmar', [OrdenServicioController::class, 'firmar'])->name('obras.ordenes-servicio.firmar');
        Route::post('/ordenes-servicio/{orden_servicio}/cumplir', [OrdenServicioController::class, 'cumplir'])->name('obras.ordenes-servicio.cumplir');
        Route::get('/mis-ordenes-servicio', [OrdenServicioController::class, 'misOrdenes'])->name('obras.mis-ordenes-servicio');
        
        Route::post('/ordenes-servicio/extraer-texto', [OrdenServicioController::class, 'extraerTexto'])
        ->name('obras.ordenes-servicio.extraer-texto');

        Route::post('/ordenes-servicio/{orden}/generar-resumen', [OrdenServicioController::class, 'generarResumen'])
        ->name('obras.ordenes-servicio.generar-resumen');
        // Ruta para crear orden de servicio desde nota de pedido
        Route::get('/ordenes-servicio/crear-desde-np/{nota}', [OrdenServicioController::class, 'createFromNotaPedido'])->name('obras.ordenes-servicio.create_from_np');
        Route::post('/ordenes-servicio/guardar-desde-np/{nota}', [OrdenServicioController::class, 'storeFromNotaPedido'])->name('obras.ordenes-servicio.store_from_np');
        

        // =============================================
        // RUTAS PARA NOTAS DE PEDIDO
        // =============================================
        Route::get('/notas-pedido', [NotaPedidoController::class, 'index'])->name('obras.notas-pedido.index');
        Route::get('/notas-pedido/crear', [NotaPedidoController::class, 'create'])->name('obras.notas-pedido.create');
        Route::post('/notas-pedido', [NotaPedidoController::class, 'store'])->name('obras.notas-pedido.store');
        Route::get('/notas-pedido/{nota}', [NotaPedidoController::class, 'show'])->name('obras.notas-pedido.show');
        Route::get('/notas-pedido/{nota}/editar', [NotaPedidoController::class, 'edit'])->name('obras.notas-pedido.edit');
        Route::put('/notas-pedido/{nota}', [NotaPedidoController::class, 'update'])->name('obras.notas-pedido.update');
        Route::post('/notas-pedido/{nota}/firmar', [NotaPedidoController::class, 'firmar'])->name('obras.notas-pedido.firmar');

        // Bandeja de entrada de notas de pedido
        Route::get('/notas/bandeja', [NotaPedidoController::class, 'bandejaEntrada'])->name('obras.notas.bandeja');

        // Rutas para extracción de texto y generación de resúmenes
        Route::post('/notas-pedido/extraer-texto', [NotaPedidoController::class, 'extraerTextoPDF'])->name('obras.notas-pedido.extraer-texto');
        Route::post('/notas-pedido/{nota}/generar-resumen', [NotaPedidoController::class, 'generarResumenAI'])->name('obras.notas-pedido.generar-resumen');

        // =============================================
        // RUTAS PARA GESTIÓN DE USUARIOS EN OBRAS
        // =============================================
        Route::get('/usuarios', [ObraController::class, 'usuarios'])->name('obras.usuarios');
        Route::post('/usuarios', [ObraController::class, 'asignarUsuario'])->name('obras.usuarios.asignar');
        Route::delete('/usuarios/{user}', [ObraController::class, 'removerUsuario'])->name('obras.usuarios.remove');

        // =============================================
        // RUTAS PARA LIBRO DE OBRA
        // =============================================
        Route::get('/libro-obra', [LibroObraController::class, 'show'])->name('libro-obra.show');
        Route::get('/libro-obra/{documentoType}/{documentoId}', [LibroObraController::class, 'showDocumento'])->name('libro-obra.documento');
        Route::get('/libro-obra/export', [LibroObraController::class, 'export'])->name('obras.libro-obra.export');
        Route::get('/libro-obra/statistics', [LibroObraController::class, 'statistics'])->name('obras.libro-obra.statistics');
        Route::get('/libro-obra/search', [LibroObraController::class, 'search'])->name('obras.libro-obra.search');
        // =============================================
        // RUTAS PARA NOTAS AL EQUIPO DE PROYECTO
        // =============================================
        Route::get('/notas-equipo-proyecto/bandeja', [NotaEquipoProyectoController::class, 'bandeja'])->name('obras.notas-equipo-proyecto.bandeja');
        Route::get('/notas-equipo-proyecto/crear', [NotaEquipoProyectoController::class, 'create'])->name('obras.notas-equipo-proyecto.create');
        Route::post('/notas-equipo-proyecto', [NotaEquipoProyectoController::class, 'store'])->name('obras.notas-equipo-proyecto.store');
        Route::get('/notas-equipo-proyecto', [NotaEquipoProyectoController::class, 'index'])->name('obras.notas-equipo-proyecto.index');
        Route::get('/notas-equipo-proyecto/{nota}', [NotaEquipoProyectoController::class, 'show'])->name('obras.notas-equipo-proyecto.show');
        Route::get('/notas-equipo-proyecto/{nota}/editar', [NotaEquipoProyectoController::class, 'edit'])->name('obras.notas-equipo-proyecto.edit');
        Route::put('/notas-equipo-proyecto/{nota}', [NotaEquipoProyectoController::class, 'update'])->name('obras.notas-equipo-proyecto.update');
        Route::post('/notas-equipo-proyecto/{nota}/firmar', [NotaEquipoProyectoController::class, 'firmar'])->name('obras.notas-equipo-proyecto.firmar');

        Route::post('/notas-equipo-proyecto/{nota}/marcar-como-leida', [NotaEquipoProyectoController::class, 'marcarComoLeida'])->name('obras.notas-equipo-proyecto.marcar-leida');
        
        // =============================================
        // RUTAS PARA ENTREGAS AL CONTRATISTA (corregidas)
        // =============================================
        Route::get('/mis-entregas-contratista', [EntregaContratistaController::class, 'index'])->name('obras.entregas-contratista.index');
        Route::get('/entregas-contratista/crear', [EntregaContratistaController::class, 'create'])->name('obras.entregas-contratista.create');
        Route::post('/entregas-contratista', [EntregaContratistaController::class, 'store'])->name('obras.entregas-contratista.store');
        Route::get('/entregas-contratista/{entrega}', [EntregaContratistaController::class, 'show'])->name('obras.entregas-contratista.show');
        Route::get('/entregas-contratista/{entrega}/editar', [EntregaContratistaController::class, 'edit'])->name('obras.entregas-contratista.edit');
        Route::put('/entregas-contratista/{entrega}', [EntregaContratistaController::class, 'update'])->name('obras.entregas-contratista.update');
        Route::post('/entregas-contratista/{entrega}/recibir', [EntregaContratistaController::class, 'recibir'])->name('obras.entregas-contratista.recibir');
        Route::get('/bandeja-entregas-contratista', [EntregaContratistaController::class, 'bandeja'])->name('obras.entregas-contratista.bandeja');
    
    });

        // RUTAS PARA ENTREGAS AL CONTRATISTA (corregidas)
   
    
});

// routes/web.php
// routes/web.php
/*use App\Models\Obra; 
Route::get('/debug-nota-pedido/{obra}', function(Obra $obra) {
    $user = auth()->user();
    $asignadoAObra = $obra->usuarios->contains($user->id);
    $tieneRolAdecuado = false;
    $rolEnObra = null;

    if ($asignadoAObra) {
        $pivot = $obra->usuarios->find($user->id)->pivot;
        if ($pivot->rol_id) {
            $rolObra = \App\Models\RoleObra::find($pivot->rol_id);
            $tieneRolAdecuado = $rolObra && in_array($rolObra->nombre, ['Jefe de Obra', 'Asistente Contratista']);
            $rolEnObra = $rolObra ? $rolObra->nombre : 'No asignado';
        }
    }

    return view('debug.nota-pedido', [
        'user' => $user,
        'obra' => $obra,
        'asignadoAObra' => $asignadoAObra,
        'rolEnObra' => $rolEnObra,
        'tieneRolAdecuado' => $tieneRolAdecuado,
        'puedeCrear' => $user->can('create', [\App\Models\Nota::class, $obra]),
    ]);
})->middleware('auth')->name('debug.nota-pedido');


Route::get('/test-policy/{obra}', function(Obra $obra) {
    $user = auth()->user();

    // Verificar si el usuario está asignado a la obra
    $asignadoAObra = $obra->usuarios->contains($user->id);

    // Obtener el rol del usuario en la obra
    $rolEnObra = null;
    $tieneRolAdecuado = false;
    $pivotData = null;

    if ($asignadoAObra) {
        $pivot = $obra->usuarios->find($user->id)->pivot;
        $pivotData = $pivot->toArray();

        if ($pivot->rol_id) {
            $rolObra = \App\Models\RoleObra::find($pivot->rol_id);
            $rolEnObra = $rolObra ? $rolObra->nombre : 'No asignado';
            $tieneRolAdecuado = $rolObra && in_array($rolObra->nombre, ['Jefe de Obra', 'Asistente Contratista']);
        }
    }

    // Verificar la política
    $puedeCrear = $user->can('create', [\App\Models\Nota::class, $obra]);

    return response()->json([
        'user_id' => $user->id,
        'user_roles' => $user->getRoleNames(),
        'obra_id' => $obra->id,
        'asignado_a_obra' => $asignadoAObra,
        'pivot_data' => $pivotData,
        'rol_en_obra' => $rolEnObra,
        'tiene_rol_adecuado' => $tieneRolAdecuado,
        'puede_crear' => $puedeCrear,
    ]);
})->middleware('auth')->name('test.policy');


Route::get('/check-db', function() {
    $obrasColumns = DB::getSchemaBuilder()->getColumnListing('obras');
    $rolesObraColumns = DB::getSchemaBuilder()->getColumnListing('roles_obra');
    $obraUsuarioRolColumns = DB::getSchemaBuilder()->getColumnListing('obra_usuario_rol');

    return [
        'obras' => $obrasColumns,
        'roles_obra' => $rolesObraColumns,
        'obra_usuario_rol' => $obraUsuarioRolColumns
    ];
});*/

