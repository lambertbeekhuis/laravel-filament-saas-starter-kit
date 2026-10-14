<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\Filament\SuperadminPanelProvider;
use App\Providers\VoltServiceProvider;

return [
    AppServiceProvider::class,
    AdminPanelProvider::class,
    SuperadminPanelProvider::class,
    VoltServiceProvider::class,
];
