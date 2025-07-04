<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Msdevbytes\CurrencySwitcher\Services\CurrencyRateService;

Route::middleware(['web'])->group(function () {
    Route::post('/currency-switcher', function (\Illuminate\Http\Request $request) {
        $request->validate(['currency' => 'required|string']);

        session()->put('currency', $request->get('currency'));

        return back();
    })->name('currency-switcher.set');
});
