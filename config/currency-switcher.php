<?php

use Filament\View\PanelsRenderHook;

return [
    'base_currency' => 'USD',
    'supported_currencies' => ['USD', 'SAR'],
    'switcher_position' => PanelsRenderHook::GLOBAL_SEARCH_BEFORE,
    'fixer_access_key' => env('FIXER_ACCESS_KEY', null),
    'ttl' => 60 * 60 * 24, // 24 hours
    'fixer_is_paid' => false,
    'use_fixer' => true,
    'fixed_rate' => [
        'USD' => 1,
        'SAR' => 3.75,
    ],

];
