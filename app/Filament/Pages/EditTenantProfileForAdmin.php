<?php

namespace App\Filament\Pages;

use App\Filament\BaseClasses\BaseTenantResource;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\EditTenantProfile;

class EditTenantProfileForAdmin extends EditTenantProfile
{
    public static function getLabel(): string
    {
        return 'My Company';
    }

    public function form(Form $form): Form
    {
        return BaseTenantResource::form($form, false);
    }
}
