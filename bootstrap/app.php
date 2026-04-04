<?php

// bootstrap/app.php
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CheckAproved;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use App\Http\Middleware\CheckNotaPedidoPermission;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Registramos los middlewares de Spatie como aliases
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
        ]);

        // Registramos nuestro middleware de aprobación
        $middleware->alias([
            'approved' => CheckAproved::class,
            'check.nota.pedido' => CheckNotaPedidoPermission::class, // Añadimos nuestro middleware
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
