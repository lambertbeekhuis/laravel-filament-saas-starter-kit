<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Mail\InviteUserToClientMail;
use App\Mail\TestMail;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Mail;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }



    // https://github.com/filamentphp/filament/discussions/285
    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction(),
            $this->getCancelFormAction(),
            Actions\Action::make('invite')
                ->hidden(fn () => !$this->record->id || (!auth()->user()->isSuperAdmin() && !$this->record->email_verified_at))
                ->label('(Re)Send Invitation')
                ->action('sendInvitation')
                ->keyBindings(['mod+shift+s'])
                //->color('gray')
            ,
        ];
    }

    public function sendInvitation(): void
    {
        $user = $this->record;
        $client = Filament::getTenant();
        $result = Mail::to($user->email)
            ->send(new InviteUserToClientMail($user, $client));



    }

}
