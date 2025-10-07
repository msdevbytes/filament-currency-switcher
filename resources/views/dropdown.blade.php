@php
    $currencySettings = app(Msdevbytes\CurrencySwitcher\Settings\CurrencySettings::class);

    $baseCurrency = Cache::get(Cache::get('currency_key'), ''); 
    if (!$baseCurrency) {
        $baseCurrency = $currencySettings->base_currency ?? 'USD';
    }

    $selectedCurrency = session('currency', $baseCurrency);
    $availableCurrencies = $currencySettings->supported_currencies ?? ['USD', 'SAR'];
@endphp

<div class="flex items-center gap-2 left-auto">
    <form method="POST" action="{{ route('currency-switcher.set') }}">
        @csrf

        <select name="currency"
            onchange="this.form.submit()"
            class="block rounded-md border-gray-300 bg-white text-sm text-gray-700 shadow-sm
                focus:border-primary-500 focus:ring-primary-500

                dark:border-gray-600 dark:bg-gray-800 dark:text-white
                dark:focus:border-primary-500 dark:focus:ring-primary-500"
        >
            @foreach($availableCurrencies as $code)
                <option value="{{ $code }}" @selected($selectedCurrency === $code)>
                    {{ $code }}
                </option>
            @endforeach
        </select>
    </form>
</div>

