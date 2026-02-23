<?php

namespace App\Providers;

use App\Models\Nota;
use App\Policies\NotaPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Nota::class => NotaPolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
