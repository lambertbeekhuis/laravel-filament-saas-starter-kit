<?php

namespace App\Filament\BaseClasses;

use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Model;

class BaseUserResource
{
    public static function form(Form $form, bool $isSuperAdmin): Form
    {
        $isTenantAdmin = !$isSuperAdmin;

        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('First Name'),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                TextInput::make('middle_name')
                    ->maxLength(255)
                    ->default(null),
                TextInput::make('last_name')
                    ->maxLength(255)
                    ->default(null),
                SpatieMediaLibraryFileUpload::make('profile_photo')
                    ->collection('profile')
                    ->hidden($isSuperAdmin)
                    // ->rules(['required'])
                    ->image(),
                DatePicker::make('date_of_birth'),

                Select::make('roles')
                    ->hidden($isSuperAdmin) // you do not know the Tenant
                    ->relationship('roles', 'name')
                    ->preload()
                    ->multiple(),
                /*
                 Did work work with the following code:
                Select::make('permissions')
                    ->hidden($isSuperAdmin) // you do not know the Tenant
                    ->relationship('permissions', 'name')
                    ->saveRelationshipsUsing(function (Model $record, $state) {
                        // $record->permissions()->syncWithPivotValues($state, ['tenant_id' => Filament::getTenant()->id]);
                    })
                    ->preload()
                    ->multiple(),
                */

                DateTimePicker::make('email_verified_at')
                    ->hidden($isTenantAdmin),
                Toggle::make('is_active')
                    ->hidden($isTenantAdmin)
                    ->required(),
                Toggle::make('is_super_admin')
                    ->hidden($isTenantAdmin)
                    ->required(),

                Toggle::make('is_active_on_tenant')
                    ->label('Allow access')
                    ->hiddenOn('create')
                    ->hidden($isSuperAdmin),
                Toggle::make('is_admin_on_tenant')
                    ->label('TenantAdmin')
                    ->hiddenOn('create')
                    ->hidden($isSuperAdmin),
                Toggle::make('sent_invitation')
                    ->label('(Re)Send invitation email')
                    // might be made hidden of already logged in once
                    ->default(false)
                    ->hidden($isSuperAdmin),
            ]);
    }

}
