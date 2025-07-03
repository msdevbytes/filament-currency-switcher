<?php

namespace Msdevbytes\CurrencySwitcher\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class InstallCurrencySwitcher extends Command
{
    protected $signature = 'currency-switcher:install';
    protected $description = 'Install and configure the Filament Currency Switcher plugin';

    public function handle()
    {
        $this->info('🔁 Installing Filament Currency Switcher...');

        $this->addSettingsClassToConfig();
        $this->publishConfig();

        $this->info('✅ Currency switcher installation complete.');
    }

    protected function addSettingsClassToConfig()
    {
        $path = config_path('settings.php');
        $filesystem = new Filesystem();

        if (! $filesystem->exists($path)) {
            $this->callSilent('vendor:publish', ['--tag' => 'settings-config']);
            $this->info('⚙️  Published Spatie settings config.');
        }

        $config = file_get_contents($path);
        $needle = "Msdevbytes\\CurrencySwitcher\\Settings\\CurrencySettings::class";

        if (! str_contains($config, $needle)) {
            $config = str_replace(
                "'settings' => [",
                "'settings' => [\n        $needle,",
                $config
            );

            file_put_contents($path, $config);
            $this->info("📝 Added CurrencySettings to config/settings.php");
        } else {
            $this->warn("ℹ️  CurrencySettings already registered in config/settings.php");
        }
    }

    protected function publishConfig()
    {
        $this->callSilent('vendor:publish', [
            '--tag' => 'currency-switcher-config',
        ]);

        $this->info('⚙️  Published currency-switcher config file.');
    }
}
