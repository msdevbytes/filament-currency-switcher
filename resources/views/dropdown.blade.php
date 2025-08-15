@php
    $selectedCurrency = session('currency', app(Msdevbytes\CurrencySwitcher\Settings\CurrencySettings::class)->base_currency ?? 'USD');
    $availableCurrencies = app(Msdevbytes\CurrencySwitcher\Settings\CurrencySettings::class)->supported_currencies ?? ['USD', 'SAR'];
@endphp

<div class="flex items-center gap-2 left-auto fi-input-wrp">
    <form method="POST" action="{{ route('currency-switcher.set') }}">
        @csrf
        <div class="fi-input-wrp-content-ctn">

            <select name="currency"
            onchange="this.form.submit()"
            class="fi-select-input block rounded-md border-gray-300 bg-white text-sm text-gray-700 shadow-sm
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
    </div>
    </form>
</div>

