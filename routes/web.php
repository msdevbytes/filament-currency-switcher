<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Msdevbytes\CurrencySwitcher\Services\CurrencyRateService;

Route::middleware(['web'])->group(function () {
    Route::post('/currency-switcher', function (\Illuminate\Http\Request $request) {
        $request->validate(['currency' => 'required|string']);
        Cache::set('currency', $request->get('currency'));
        session()->put('currency', $request->get('currency'));

        return back();
    })->name('currency-switcher.set');
});
