<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Notifications\SentInvitationToUserNotification;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Notification;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $data['password'] = 'to be generated';
        $record = parent::handleRecordCreation($data);

        if ($data['sent_invitation'] ?? false) {
            $tenant = Filament::getTenant();
            Notification::send($record, new SentInvitationToUserNotification($record, $tenant));
        }
        return $record;
    }

}
