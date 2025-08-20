<?php

namespace Msdevbytes\CurrencySwitcher\Infolists\Components;

use Filament\Infolists\Components\TextEntry;
use Msdevbytes\CurrencySwitcher\Services\CurrencyRateService;
use CodeDistortion\Currency\Currency;
use Illuminate\Support\Number;

class CurrencyEntry extends TextEntry
{
    protected bool $showOriginal = false;

    public function withOriginal(bool $condition = true): static
    {
        $this->showOriginal = $condition;

        return $this->state(function ($state) {
            if (! is_numeric($state)) return $state;

            $currency = session('currency', config('app.currency', 'USD'));
            $baseCurrency = config('app.currency', 'USD');

            $converted = app(CurrencyRateService::class)->convert($state, $currency);
            $formatted = (new Currency($converted, $currency))->format();

            if ($this->showOriginal && $currency !== $baseCurrency) {
                $formatted .= ' (' . $baseCurrency . ' ' . number_format($state, 2) . ')';
            }

            return $formatted;
        });
    }

    protected function setUp(): void
    {
        parent::setUp();

        $currency = session('currency', config('app.currency', 'USD'));
        // Default formatter if withOriginal() isn't called
        $this->formatStateUsing(function ($state) use ($currency) {
            $converted = app(CurrencyRateService::class)->convert($state, $currency);
            return Number::format($converted);
        })->icon(function (string $state) use ($currency) {
            if ($currency === 'SAR') {
                return 'icon-riyal';
            } else {
                return 'bx-dollar';
            }
        });
    }
}
