<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\VerificationController;
use App\Models\Role;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProfileController;


/*Route::get('/', function () {
    return view('welcome');
});

Auth::routes();
Auth::routes(['verify' => true]);
// Ruta para enviar email de verificación manualmente
Route::post('/email/resend', [VerificationController::class, 'resend'])->name('verification.resend');

// Ruta para verificar email
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
    ->name('verification.verify');

//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Rutas que requieren autenticación y aprobación
Route::middleware(['auth', 'approved'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    // Rutas de gestión de usuarios
    Route::resource('users', UserController::class);

    // Ruta para aprobar usuarios
    Route::patch('/users/{user}/approve', [UserController::class, 'approve'])->name('users.approve');

    // Rutas de notas
    Route::resource('notas', NotaController::class);

    // Rutas adicionales de notas
    Route::post('/notas/import', [NotaController::class, 'import'])->name('notas.import');
    Route::get('/notas/export', [NotaController::class, 'export'])->name('notas.export');
    //Route::post('/notas/{nota}/generar-resumen-ai', [NotaController::class, 'generarResumenAI'])->name('notas.generar-resumen-ai');
    Route::post('/notas/{nota}/generar-resumen-ai', [NotaController::class, 'generarResumenAITemporal'])->name('notas.generar-resumen-ai');
    Route::get('/notas/{nota}/extraer-texto-pdf', [NotaController::class, 'extraerTextoPDF'])->name('notas.extraer-texto-pdf');
    Route::post('/notas/{nota}/subir-pdf', [NotaController::class, 'subirPDF'])->name('notas.subir-pdf');
    Route::post('/subir-pdf-temporal', [NotaController::class, 'subirPDFTemporal'])->name('notas.subir-pdf-temporal');
    Route::post('/generar-resumen-ai-temporal', [NotaController::class, 'generarResumenAITemporal'])->name('notas.generar-resumen-ai-temporal');
    Route::get('/listar-modelos-gemini', [NotaController::class, 'listarModelosGemini']);
});*/



// Ruta principal
Route::get('/', function () {
    return view('welcome');
});

// Rutas de autenticación
// Rutas de autenticación
Auth::routes(['verify' => true]);

Route::get('/unapproved', [LoginController::class, 'showUnapprovedMessage'])->name('unapproved');

// Ruta para verificar email (debe estar fuera del middleware auth)
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
    ->middleware(['signed'])
    ->name('verification.verify');

// Ruta para reenviar email de verificación (requiere autenticación)
Route::post('/email/resend', [VerificationController::class, 'resend'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.resend');

// Ruta para mostrar la vista de verificación (requiere autenticación)
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
/*Route::middleware(['auth', 'approved'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    // Rutas de gestión de usuarios
    Route::resource('users', UserController::class);
    Route::patch('/users/{user}/approve', [UserController::class, 'approve'])->name('users.approve');

    // Rutas de notas
    Route::resource('notas', NotaController::class);
    Route::post('/notas/import', [NotaController::class, 'import'])->name('notas.import');
    //Route::get('/notas/export', [NotaController::class, 'export'])->name('notas.export');
    // Rutas de notas
    Route::get('/notas/export', [NotaController::class, 'export'])->name('notas.export');
    Route::resource('notas', NotaController::class);

    Route::post('/notas/{nota}/generar-resumen-ai', [NotaController::class, 'generarResumenAITemporal'])->name('notas.generar-resumen-ai');
    Route::get('/notas/{nota}/extraer-texto-pdf', [NotaController::class, 'extraerTextoPDF'])->name('notas.extraer-texto-pdf');
    Route::post('/notas/{nota}/subir-pdf', [NotaController::class, 'subirPDF'])->name('notas.subir-pdf');
    Route::post('/subir-pdf-temporal', [NotaController::class, 'subirPDFTemporal'])->name('notas.subir-pdf-temporal');
    Route::post('/generar-resumen-ai-temporal', [NotaController::class, 'generarResumenAITemporal'])->name('notas.generar-resumen-ai-temporal');
    Route::get('/listar-modelos-gemini', [NotaController::class, 'listarModelosGemini']);
});*/

// Rutas que requieren autenticación y aprobación
Route::middleware(['auth', 'approved'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    // Rutas de gestión de usuarios
    Route::resource('users', UserController::class);
    Route::patch('/users/{user}/approve', [UserController::class, 'approve'])->name('users.approve');

    // Rutas de notas - Primero las rutas específicas, luego el resource
    Route::get('/notas/export', [NotaController::class, 'export'])->name('notas.export');
   

    Route::post('/notas/import', [NotaController::class, 'import'])->name('notas.import');
    Route::post('/notas/{nota}/generar-resumen-ai', [NotaController::class, 'generarResumenAITemporal'])->name('notas.generar-resumen-ai');
    Route::get('/notas/{nota}/extraer-texto-pdf', [NotaController::class, 'extraerTextoPDF'])->name('notas.extraer-texto-pdf');
    Route::post('/notas/{nota}/subir-pdf', [NotaController::class, 'subirPDF'])->name('notas.subir-pdf');
    Route::post('/subir-pdf-temporal', [NotaController::class, 'subirPDFTemporal'])->name('notas.subir-pdf-temporal');
    Route::post('/generar-resumen-ai-temporal', [NotaController::class, 'generarResumenAITemporal'])->name('notas.generar-resumen-ai-temporal');
    Route::get('/listar-modelos-gemini', [NotaController::class, 'listarModelosGemini']);

    // Ruta resource debe ir al final
    Route::resource('notas', NotaController::class)->except(['show']);
});

