<?php

if (! function_exists('format_currency')) {
    function format_currency($amount): string
    {
        $currency = session('currency', config('app.currency', 'USD'));

        $symbols = [
            'USD' => '$',
            'SAR' => '',
        ];

        $symbol = $symbols[$currency] ?? $currency;

        return $symbol . number_format($amount, 2);
    }
}
