<?php

namespace Msdevbytes\CurrencySwitcher\Tables\Columns;

use CodeDistortion\Currency\Currency;
use Filament\Tables\Columns\TextColumn;
use Msdevbytes\CurrencySwitcher\Services\CurrencyRateService;

class CurrencyColumn extends TextColumn
{
    protected bool $showOriginal = false;

    public function withOriginal(bool $condition = true): static
    {
        $this->showOriginal = $condition;

        return $this->formatStateUsing(function ($state) {
            if (! is_numeric($state)) return $state;

            $currency = session('currency', config('app.currency', 'USD'));
            $baseCurrency = config('app.currency', 'USD');

            $converted = app(CurrencyRateService::class)->convert($state, $currency);
            $formatted = format_currency($converted);

            if ($this->showOriginal && $currency !== $baseCurrency) {
                $formatted .= ' (' . $baseCurrency . ' ' . number_format($state, 2) . ')';
            }

            return $formatted;
        });
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Default formatter if withOriginal() isn't called
        $this->formatStateUsing(function ($state) {
            if (! is_numeric($state)) return $state;

            $currency = session('currency', config('app.currency', 'USD'));

            $converted = app(CurrencyRateService::class)->convert($state, $currency);
            $currency = session('currency', config('app.currency', 'USD'));
            $cur = new Currency($converted, $currency);
            return $cur->format();
        });
    }
}
