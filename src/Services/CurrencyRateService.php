<?php

namespace Msdevbytes\CurrencySwitcher\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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
            if (config('currency-switcher.fixer_is_paid', false) && config('currency-switcher.base_currency', '')) {
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
            if (!config('currency-switcher.fixer_is_paid', false) && config('currency-switcher.base_currency', []) && in_array('EUR', config('currency-switcher.base_currency', []))) {
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

    public function convert(float|int $amount, string $toCurrency): float
    {
        $rates = $this->getRates();
        return round($amount * ($rates[$toCurrency] ?? 1), 2);
    }
}
