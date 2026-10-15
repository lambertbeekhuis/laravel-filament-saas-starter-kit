<?php

namespace App\Actions;

use App\Events\RegisteredTenantUser;
use App\Models\Tenant;
use App\Models\TenantUser;
use App\Models\User;
use App\Support\Features;
use Illuminate\Support\Facades\DB;

class RegisterUserWithNewTenant
{
    /**
     * @param  array{name: string, email: string, password: string, tenant_name: string}  $data
     * @return array{user: User, tenant: Tenant, tenantUser: TenantUser}
     */
    public function register(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $tenant = Tenant::create([
                'name' => $data['tenant_name'],
                'registration_type' => Tenant::REGISTRATION_TYPE_PUBLIC_DIRECT,
            ]);

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
            ]);

            $tenantUser = TenantUser::create([
                'tenant_id' => $tenant->id,
                'user_id' => $user->id,
                'is_active_on_tenant' => true,
            ]);

            if (Features::permissionsEnabled()) {
                setPermissionsTeamId($tenant->id);
                $user->assignRole('admin');
            }

            event(new RegisteredTenantUser($tenantUser));

            return [
                'user' => $user,
                'tenant' => $tenant,
                'tenantUser' => $tenantUser,
            ];
        });
    }
}
