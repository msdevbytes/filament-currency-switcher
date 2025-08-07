<?php

namespace Msdevbytes\CurrencySwitcher;

use Filament\Facades\Filament;
use Filament\View\PanelsRenderHook;
use Msdevbytes\CurrencySwitcher\Console\Commands\InstallCurrencySwitcher;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class CurrencyServiceProvider extends PackageServiceProvider
{
    protected array $plugins = [
        CurrencySwitcherPlugin::class,
    ];

    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-currency-switcher')
            ->hasViews()
            ->hasRoutes('web');
    }

    public function boot(): void
    {


        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'currency-switcher');

        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCurrencySwitcher::class,
            ]);
        }

        if (file_exists(__DIR__ . '/../routes/web.php')) {
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        }

        $this->mergeConfigFrom(__DIR__ . '/../config/currency-switcher.php', 'currency-switcher');

        $this->publishes([
            __DIR__ . '/../config/currency-switcher.php' => config_path('currency-switcher.php'),
        ], 'currency-switcher-config');

        Filament::registerRenderHook(config('currency-switcher.switcher_position', PanelsRenderHook::GLOBAL_SEARCH_BEFORE), fn() => view('currency-switcher::dropdown'));
    }

    public function register(): void
    {
        // 
    }
}
