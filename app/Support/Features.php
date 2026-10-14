<?php

namespace App\Support;

class Features
{
    public static function permissionsEnabled(): bool
    {
        return (bool) config('features.permissions.enabled', true);
    }
}
