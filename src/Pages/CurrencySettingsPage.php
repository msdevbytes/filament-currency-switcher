<?php

namespace Msdevbytes\CurrencySwitcher\Pages;

use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Alignment;
use Illuminate\Contracts\View\View;
use Illuminate\Support\HtmlString;
use Livewire\Component;
use Msdevbytes\CurrencySwitcher\Settings\CurrencySettings;

class CurrencySettingsPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-currency-dollar';
    protected static string | \UnitEnum | null $navigationGroup = 'System';
    protected static ?string $navigationLabel = 'Currency Settings';
    protected static ?string $slug = 'currency-settings';


    protected string $view = 'currency-switcher::pages.settings';


    public $fixer_api_key;
    public $fixer_is_paid;
    public $base_currency;
    public $supported_currencies = [];


    public function mount(): void
    {
        $settings = app(CurrencySettings::class);

        $this->form->fill([
            'fixer_is_paid' => $settings->fixer_is_paid,
            'fixer_api_key' => $settings->fixer_api_key,
            'base_currency' => $settings->base_currency,
            'supported_currencies' => $settings->supported_currencies,
        ]);
    }

    protected function getFormActions($form)
    {
        Actions::make([])->alignment(Alignment::End);
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make()->schema([
                Toggle::make('fixer_is_paid')
                    ->label('Using paid Fixer plan?')
                    ->helperText('Enable only if your Fixer account supports setting custom base currencies.')
                    ->default(false),

                TextInput::make('fixer_api_key')
                    ->label('Fixer API Key')
                    ->helperText(new HtmlString('<a href="https://fixer.io/" target="_blank" class="hover:text-blue-600">fixer.io (Foreign exchange rates and currency conversion api)</a>'))
                    ->required(),

                Select::make('base_currency')
                    ->options(function (): array {
                        $cur = [];
                        foreach (config('currency-switcher.base_currency', ['USD', 'JPY', 'SAR', 'PKR']) as $value) {
                            $cur[$value] = $value;
                        }
                        return $cur;
                    })
                    ->native(false)
                    ->required(),

                Select::make('supported_currencies')
                    ->label('Supported Currencies')
                    ->searchable()
                    ->native(false)
                    ->multiple()
                    ->options(function (): array {
                        $cur = [];
                        foreach (config('currency-switcher.supported_currencies', ['USD', 'JPY', 'SAR', 'PKR']) as $value) {
                            $cur[$value] = $value;
                        }
                        return $cur;
                    })
                    ->columns(2)
                    ->required(),
            ])->columns(2)
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        /** @var CurrencySettings $settings */
        $settings = app(CurrencySettings::class);

        $settings->fixer_is_paid = $data['fixer_is_paid'];
        $settings->fixer_api_key = $data['fixer_api_key'];
        $settings->base_currency = $data['base_currency'];
        $settings->supported_currencies = $data['supported_currencies'];
        $settings->save();

        Notification::make()
            ->title('Currency settings updated')
            ->success()
            ->send();
    }
}
