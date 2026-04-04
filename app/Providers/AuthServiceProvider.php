<?php

// app/Providers/AuthServiceProvider.php
namespace App\Providers;

use App\Models\Nota;
use App\Policies\NotaPedidoPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Nota::class => NotaPedidoPolicy::class, // Asegúrate de que solo esté esta línea
        \App\Models\NotaEquipoProyecto::class => \App\Policies\NotaEquipoProyectoPolicy::class,
        \App\Models\EntregaContratista::class => \App\Policies\EntregaContratistaPolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();
        Gate::define('view-all-obras', function ($user) {
            return $user->hasRole('admin');
        });
    }
}


