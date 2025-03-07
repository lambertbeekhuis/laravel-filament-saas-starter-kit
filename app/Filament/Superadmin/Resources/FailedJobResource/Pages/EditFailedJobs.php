<?php

namespace App\Filament\SuperAdmin\Resources\FailedJobResource\Pages;

use App\Filament\SuperAdmin\Resources\FailedJobResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFailedJobs extends EditRecord
{
    protected static string $resource = FailedJobResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        $resource = static::getResource();
        return $resource::getUrl('index');
    }


}
