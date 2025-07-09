# 💱 Filament Currency Switcher

A Filament v3 plugin to add a currency switcher with automatic rate conversion using the [Fixer.io](https://fixer.io/) API.

---

## 🚀 Features

- 💸 Currency dropdown in the Filament navbar
- 🔁 Automatic rate conversion using Fixer API
- ✅ Supports free and paid Fixer plans
- 🎛️ Configuration via Filament settings page
- 🌙 Dark mode ready
- 🧩 Powered by [spatie/laravel-settings](https://github.com/spatie/laravel-settings)

---

## 📦 Installation

### 1. Install via Composer

```bash
composer require msdevbytes/filament-currency-switcher
```

### 2. Run the Installer

- Register the settings class
- Publish the config file

```bash
php artisan currency-switcher:install
```

### 3. Run these for spatie/laravel-settings:

```bash
php artisan vendor:publish --provider="Spatie\LaravelSettings\LaravelSettingsServiceProvider" --tag="migrations"

php artisan vendor:publish --provider="Spatie\LaravelSettings\LaravelSettingsServiceProvider" --tag="config"
```

```bash
php artisan make:settings-migration CurrencySettings
```

### 4. Replace the code inside the //database/settings/2025_07_03_130553_currency_settings.php

```
<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('currency.fixer_api_key', 'api-key');
        $this->migrator->add('currency.base_currency', 'USD');
        $this->migrator->add('currency.supported_currencies', config('currency-switcher.supported_currencies'));
        $this->migrator->add('currency.fixer_is_paid', false);
    }
};
```

### 5. Run Migrations (if using Spatie settings for the first time)

```bash
php artisan vendor:publish --tag=settings-migrations
php artisan migrate
```
