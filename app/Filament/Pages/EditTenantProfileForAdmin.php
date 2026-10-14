<?php

namespace App\Filament\Pages;

use App\Filament\BaseClasses\BaseTenantResource;
use Filament\Pages\Tenancy\EditTenantProfile;
use Filament\Schemas\Schema;

class EditTenantProfileForAdmin extends EditTenantProfile
{
    public static function getLabel(): string
    {
        return 'My Company';
    }

    public function form(Schema $schema): Schema
    {
        return BaseTenantResource::form($schema, false)
            ->columns([
                'default' => 1,
                'lg' => 2,
                'xl' => 2,
                '2xl' => 2,
            ]);
    }
}
