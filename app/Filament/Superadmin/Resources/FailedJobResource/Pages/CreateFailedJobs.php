<?php

namespace App\Filament\SuperAdmin\Resources\FailedJobResource\Pages;

use App\Filament\SuperAdmin\Resources\FailedJobResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateFailedJobs extends CreateRecord
{
    protected static string $resource = FailedJobResource::class;

    protected function getRedirectUrl(): string
    {
        $resource = static::getResource();
        return $resource::getUrl('index');
    }


}
