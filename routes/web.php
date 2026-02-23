<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;


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
    Route::get('/home', [HomeController::class, 'index'])->name('home');

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
    Route::get('/listar-modelos-gemini', [NotaController::class, 'listarModelosGemini'])->name('notas.listar-modelos-gemini');

    // Ruta para ver una nota específica
   // Route::get('/notas/{nota}', [NotaController::class, 'show'])->name('notas.show');

    // Ruta resource para notas (sin excluir ningún método)
    //Route::resource('notas', NotaController::class);

   // Rutas de notas - Todas explícitas
   Route::get('/notas', [NotaController::class, 'index'])->name('notas.index');
   Route::get('/notas/create', [NotaController::class, 'create'])->name('notas.create');
   Route::post('/notas', [NotaController::class, 'store'])->name('notas.store');
   Route::get('/notas/{nota}', [NotaController::class, 'show'])->name('notas.show');
   Route::get('/notas/{nota}/edit', [NotaController::class, 'edit'])->name('notas.edit');
   Route::put('/notas/{nota}', [NotaController::class, 'update'])->name('notas.update');
   Route::delete('/notas/{nota}', [NotaController::class, 'destroy'])->name('notas.destroy');
});

