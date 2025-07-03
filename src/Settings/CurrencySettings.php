<?php

namespace Msdevbytes\CurrencySwitcher\Settings;

use Spatie\LaravelSettings\Settings;

class CurrencySettings extends Settings
{

    public string $fixer_api_key = '';
    public bool $fixer_is_paid = false;
    public string $base_currency = 'USD';
    public array $supported_currencies = ['USD', 'SAR'];


    public static function group(): string
    {
        return 'currency';
    }
}
