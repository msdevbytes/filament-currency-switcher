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

### 3. Run Migrations (if using Spatie settings for the first time)

```bash
php artisan vendor:publish --tag=settings-migrations
php artisan migrate
```
