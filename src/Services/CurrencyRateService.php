<?php

namespace Msdevbytes\CurrencySwitcher\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Number;
use Msdevbytes\CurrencySwitcher\Settings\CurrencySettings;

class CurrencyRateService
{
    protected string $cacheKey = 'currency_switcher.rates';


    public function getRates(): array
    {
        /** @var CurrencySettings $settings */
        $ttl = config('currency-switcher.ttl', 60 * 60 * 24);

        return Cache::remember($this->cacheKey, $ttl, function () {
            $apiKey = config('currency-switcher.fixer_access_key', '');

            if ($apiKey == "") {
                Log::error('Fixer API key not set please set FIXER_ACCESS_KEY in your .env file');
                return [];
            }
            $symbols = implode(',', config('currency-switcher.supported_currencies', []));

            $params = [
                'access_key' => $apiKey,
                'symbols' => $symbols,
            ];

            // Only include base if the user is on a paid Fixer plan
            if (config('currency-switcher.fixer_is_paid', false) && config('currency-switcher.base_currency', 'USD')) {
                $params['base'] = config('currency-switcher.base_currency');
            }

            $response = Http::get('http://data.fixer.io/api/latest', $params);

            Log::debug('Fixer API response', ['response' => $response->json()]);

            if (! $response->ok() || ! isset($response['rates'])) {
                Log::error('Fixer API failed', ['response' => $response->json()]);
                return [];
            }

            $rates = $response['rates'];


            // 🔁 Normalize manually if on free plan with non-EUR base
            if (!config('currency-switcher.fixer_is_paid', false) && config('currency-switcher.base_currency', '') && 'EUR' == config('currency-switcher.base_currency', '')) {
                $eurToBase = $rates[config('currency-switcher.base_currency')] ?? 1;

                foreach ($rates as $code => $rate) {
                    $rates[$code] = $rate / $eurToBase;
                }

                // Manually mark base as 1
                $rates[config('currency-switcher.base_currency')] = 1;
            }
            return $rates;
        });
    }

    // Normalize amount strings like "1,234.56" or "SAR 1 234.56" to float
    private function normalize(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return 0;
        }
        if (is_numeric($value)) {
            return (float) $value;
        }
        $clean = preg_replace('/[^\d.\-]/', '', (string) $value); // keep digits, dot, minus
        return $clean === '' ? 0 : (float) $clean;
    }

    public function convert(float|int|string $amount, string $toCurrency): float
    {
        $amount = $this->normalize($amount);

        $rates = [];

        if (config('currency-switcher.use_fixer', false) && !config('currency-switcher.fixer_is_paid', false)) {
            // Use fixed rates if not using Fixer
            $rates = config('currency-switcher.fixed_rate', []);
        } else {
            $rates = $this->getRates();
        }
        if ($toCurrency == config('currency-switcher.base_currency', 'USD')) {
            return $amount; // Return original amount if unsupported
        }
        return (round($amount * ($rates[$toCurrency] ?? 1), 2));
    }
}
