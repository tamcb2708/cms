<?php

namespace App\Providers;

use App\Http\Responses\Auth\LoginResponse;
use Filament\Http\Responses\Auth\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Support\ServiceProvider;

use Livewire\Livewire;
use Illuminate\Support\Facades\Route;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(LoginResponseContract::class, LoginResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\Event::listen(\Illuminate\Auth\Events\Login::class, function (\Illuminate\Auth\Events\Login $event) {
            \App\Jobs\WarmUpSystemCache::dispatch($event->user);
        });
    }
}
