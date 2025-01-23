<?php

namespace App\Filament\BaseClasses;

use App\Models\Tenant;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;

class BaseTenantResource
{

    public static function form(Form $form, bool $isSuperAdmin): Form
    {
        $isTenantAdmin = !$isSuperAdmin;

        return $form
            ->columns([
                'default' => 1,
                'lg' => 2,
                'xl' => 2,
                '2xl' => 2,
            ])
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Placeholder::make('empty')
                    ->label(''),
                Toggle::make('is_active')
                    ->hidden($isTenantAdmin)
                    ->required(),
                Select::make('registration_type')
                    ->required()
                    ->options(Tenant::getRegistrationTypes()),
                TextInput::make('address')
                    ->maxLength(255)
                    ->default(null),
                TextInput::make('zip')
                    ->maxLength(255)
                    ->default(null),
                TextInput::make('city')
                    ->maxLength(255)
                    ->default(null),
                TextInput::make('country')
                    ->maxLength(255)
                    ->default(null),
                SpatieMediaLibraryFileUpload::make('logo')
                    ->collection('logo')
                    ->image(),
            ]);
    }

}
