<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\TicketHead;
use App\Models\TicketDetail;
use App\Observers\TicketHeadObserver;
use App\Observers\TicketDetailObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        TicketHead::observe(TicketHeadObserver::class);
        TicketDetail::observe(TicketDetailObserver::class);
    }
}
