<?php

namespace App\Filament\Pages;

use App\Settings\BusinessInformationSetting;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManageBusinessInformation extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'Settings';

    protected static string $settings = BusinessInformationSetting::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([

                TextInput::make('name')
                    ->label('Name')
                    ->required(),

                TextInput::make('address')
                    ->label('Addresss')
                    ->required(),

                FileUpload::make('logo')
                    ->label('Logo')
                    ->required(),

                TextInput::make('contact')
                    ->label('Phone number')
                    ->tel()
                    ->required(),
                Select::make('type')
                    ->options([
                        0 => 'Individual',
                        1 => 'Sole Partnership',
                        2 => 'Corporation',
                        3 => 'Partnership',
                    ]),
            ])->columns(1);
    }
}
