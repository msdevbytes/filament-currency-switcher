<?php

namespace Msdevbytes\CurrencySwitcher;

use Filament\PluginServiceProvider;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Msdevbytes\CurrencySwitcher\Pages\CurrencySettingsPage;

class CurrencySwitcherPlugin implements Plugin
{
    public const POSITION_TOP_LEFT  = 'top-left';
    public const POSITION_TOP_RIGHT = 'top-right';

    protected string $position = self::POSITION_TOP_LEFT;

    public static function make(): static
    {
        return app(static::class);
    }

    public function position(string $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function getPosition(): string
    {
        return $this->position;
    }

    public function getId(): string
    {
        return 'currency-switcher';
    }

    public function register(Panel $panel): void
    {
        // Register settings page with the panel
        $panel->pages([
            CurrencySettingsPage::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
