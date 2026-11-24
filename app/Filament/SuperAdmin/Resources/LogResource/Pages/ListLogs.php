<?php

namespace App\Filament\SuperAdmin\Resources\LogResource\Pages;

use App\Filament\SuperAdmin\Resources\LogResource;
use Filament\Resources\Pages\ListRecords;

class ListLogs extends ListRecords
{
    protected static string $resource = LogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
