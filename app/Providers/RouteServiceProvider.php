<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use App\Models\TicketHead;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Route::bind('ticket', function ($value) {
            return TicketHead::where('slug', $value)
                ->orWhere('id', $value)
                ->firstOrFail();
        });
    }
}
