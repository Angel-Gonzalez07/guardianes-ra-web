<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // 5 intentos por minuto por correo + IP (frena la adivinación de contraseñas)
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by(strtolower((string) $request->input('email')) . '|' . $request->ip());
        });

        // 5 registros cada 10 minutos por IP
        RateLimiter::for('registro', function (Request $request) {
            return Limit::perMinutes(10, 5)->by($request->ip());
        });
    }
}
