<?php

namespace App\Providers;

use App\Services\PengaturanService;
use App\Support\Menu;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PengaturanService::class);
    }

    public function boot(): void
    {
        // Paksa eager loading saat pengembangan (mencegah query N+1).
        Model::preventLazyLoading(! $this->app->isProduction());

        Paginator::useTailwind();

        View::composer('components.layouts.app', function ($view) {
            $user = auth()->user();
            $view->with('menu', Menu::for($user));
            $view->with('roleLabel', $user?->roleEnum()?->label());
        });

        View::composer('components.layouts.public', function ($view) {
            $view->with('kontak', app(PengaturanService::class)->semua());
        });
    }
}
