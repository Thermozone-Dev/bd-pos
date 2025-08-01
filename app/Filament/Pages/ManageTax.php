<?php

namespace App\Filament\Pages;

use App\Settings\TaxSetting;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Filament\Support\Exceptions\Halt;
use Illuminate\Database\Eloquent\Model;
use Throwable;

class ManageTax extends SettingsPage
{
    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';


    protected static ?string $navigationGroup = 'Settings';


    protected static string $settings = TaxSetting::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Toggle::make('vat_adjustments')
                    ->label('VAT Adjustments')
                    ->default(0)
                    ->required(),

                Toggle::make('vat_deduction')
                    ->label('VAT Deduction')
                    ->default(0)
                    ->required(),


                Toggle::make('amusement_tax')
                    ->label('Amusement Tax')
                    ->default(0)
                    ->required(),


                Toggle::make('is_inclusive')
                    ->label('Exclusive / Inclusive')
                    ->live()
                    ->hint(fn ($state) =>($state) ? 'Current Selection: Inclusive' : 'Current Selection: Exclusive')
                    ->hintColor('success')
                    ->helperText( fn ($state) => ($state) ? 'Turn off to set as exclusive' : 'Turn on to set as inclusive')
                    ->default(1)
                    ->required(),

            ])->columns(2);
    }

}
