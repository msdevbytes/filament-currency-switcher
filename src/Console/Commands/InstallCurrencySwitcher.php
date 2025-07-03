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
        $this->createSettingsMigration();

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

    protected function createSettingsMigration()
    {
        $this->callSilent('make:settings-migration', [
            'name' => 'CurrencySettings',
        ]);

        $filesystem = new Filesystem();
        $migrationPath = base_path('database/settings');

        $migrationFiles = collect($filesystem->files($migrationPath))
            ->sortByDesc(fn($file) => $file->getCTime())
            ->filter(fn($file) => str_contains($file->getFilename(), 'currency_settings'))
            ->values();

        $latestFile = $migrationFiles->first();

        if ($latestFile && $filesystem->exists($latestFile->getPathname())) {
            $contents = $filesystem->get($latestFile->getPathname());

            $injection = <<<PHP
                    \$this->migrator->add('currency.fixer_api_key', 'api-key');
                    \$this->migrator->add('currency.base_currency', '');
                    \$this->migrator->add('currency.supported_currencies', []);
                    \$this->migrator->add('currency.fixer_is_paid', false);
            PHP;

            // Replace inside `up()` method
            $contents = preg_replace(
                '/function up\(\)\s*\{/',
                "function up()\n    {\n$injection\n",
                $contents
            );

            $filesystem->put($latestFile->getPathname(), $contents);

            $this->info('🗃️  Created settings migration and added default keys.');
        } else {
            $this->warn('⚠️ Could not locate the newly created settings migration.');
        }
    }
}
