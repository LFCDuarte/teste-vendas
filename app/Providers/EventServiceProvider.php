<?php

namespace App\Providers;

use App\Models\Produto;
use App\Models\VendaProduto;
use App\Observers\ProdutoObserver;
use App\Observers\VendaProdutoObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    public function boot(): void
    {
        Produto::observe(ProdutoObserver::class);
        VendaProduto::observe(VendaProdutoObserver::class);
    }

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
