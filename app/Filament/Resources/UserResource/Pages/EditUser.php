<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Mail\InviteUserToTenantMail;
use App\Mail\TestMail;
use App\Models\Tenant;
use App\Models\TenantUser;
use App\Notifications\SentInvitationToUserNotification;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }


    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $data;
    }


    protected function mutateFormDataBeforeFill(array $data): array
    {
        $tenant = Filament::getTenant();
        $record = $this->record;

        $tenantUser = TenantUser::findOneForUserAndTenant($this->record->id, $tenant->id);
        $data['is_active_on_tenant'] = $tenantUser->is_active_on_tenant;
        $data['is_admin_on_tenant'] = $tenantUser->is_admin_on_tenant;
        return $data;
    }


    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record =  parent::handleRecordUpdate($record, $data);

        $tenant = Filament::getTenant();
        $tenantUser = TenantUser::findOneForUserAndTenant($this->record->id, $tenant->id);
        $tenantUser->update([
            'is_active_on_tenant' => $data['is_active_on_tenant'],
            'is_admin_on_tenant' => $data['is_admin_on_tenant'],
        ]);

        if ($data['sent_invitation'] ?? false) {
            $tenant = Filament::getTenant();
            Notification::send($record, new SentInvitationToUserNotification($record, $tenant));
        }

        return $record;
    }

    // https://github.com/filamentphp/filament/discussions/285
    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction(),
            $this->getCancelFormAction(),
            /*
             * Deprecated, used an in-form switch instead
            Actions\Action::make('invite')
                ->hidden(fn () => !$this->record->id || (!auth()->user()->isSuperAdmin() && !$this->record->email_verified_at))
                ->label('(Re)Send Invitation')
                ->action('sendInvitation')
                ->keyBindings(['mod+shift+s'])
                //->color('gray')
            ,
            */
        ];
    }

    /**
     * @deprecated
     */
    public function sendInvitation(): void
    {
        $user = $this->record;
        $tenant = Filament::getTenant();
        $result = Mail::to($user->email)
            ->send(new InviteUserToTenantMail($user, $tenant));

    }

}
