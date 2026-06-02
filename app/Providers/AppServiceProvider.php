<?php

namespace App\Providers;

use Filament\Auth\Http\Responses\Contracts\LogoutResponse;
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(LogoutResponse::class, \App\Filament\Auth\Responses\LogoutResponse::class);
    }

    public function boot(): void
    {
        FilamentView::registerRenderHook('head.end', fn (): string => Blade::render('@include(\'vendor.filament.components.pwa-head\')'));
    }
}
