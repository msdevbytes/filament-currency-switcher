<?php

namespace Msdevbytes\CurrencySwitcher\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Msdevbytes\CurrencySwitcher\Settings\CurrencySettings;

class CurrencyRateService
{
    protected string $cacheKey = 'currency_switcher.rates';
    protected int $ttl = 60 * 60 * 24; // 24 hours

    public function getRates(): array
    {
        /** @var CurrencySettings $settings */
        $settings = app(CurrencySettings::class);

        return Cache::remember($this->cacheKey, $this->ttl, function () use ($settings) {
            $apiKey = $settings->fixer_api_key;
            $symbols = implode(',', $settings->supported_currencies);

            $params = [
                'access_key' => $apiKey,
                'symbols' => $symbols,
            ];

            // Only include base if the user is on a paid Fixer plan
            if ($settings->fixer_is_paid && $settings->base_currency) {
                $params['base'] = $settings->base_currency;
            }

            $response = Http::get('http://data.fixer.io/api/latest', $params);

            Log::debug('Fixer API response', ['response' => $response->json()]);

            if (! $response->ok() || ! isset($response['rates'])) {
                Log::error('Fixer API failed', ['response' => $response->json()]);
                return [];
            }

            $rates = $response['rates'];

            // 🔁 Normalize manually if on free plan with non-EUR base
            if (! $settings->fixer_is_paid && $settings->base_currency && $settings->base_currency !== 'EUR') {
                $eurToBase = $rates[$settings->base_currency] ?? 1;

                foreach ($rates as $code => $rate) {
                    $rates[$code] = $rate / $eurToBase;
                }

                // Manually mark base as 1
                $rates[$settings->base_currency] = 1;
            }

            return $rates;
        });
    }

    public function convert(float|int $amount, string $toCurrency): float
    {
        $rates = $this->getRates();

        return round($amount * ($rates[$toCurrency] ?? 1), 2);
    }
}
