<?php

use Filament\View\PanelsRenderHook;

return [
    'base_currency' => ['USD', 'SAR'],
    'supported_currencies' => ['USD', 'SAR'],
    'swicher_position' => PanelsRenderHook::GLOBAL_SEARCH_BEFORE,
    'fixer_api_key' => env('FIXER_API_KEY', null)
];
