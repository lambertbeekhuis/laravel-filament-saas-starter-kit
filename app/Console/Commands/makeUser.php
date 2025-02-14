<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Tenant;
use App\Models\TenantUser;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Facades\Hash;

class makeUser extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:make-user {email : login email of the user} {name : (first)name of the user} {password : password}  {tenantName : company name of the user (=tenantName}  {--tenantAdmin : is the user a TenantAdmin }  {--superAdmin : is the user a superAdmin} {--verified : set email to verified}';

    /*
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make a new User and Tenant for the SaasSetup app:make-user {email} {name} {password} {tenantName} {--tenantAdmin} {--superAdmin} {--verified}';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $name = $this->argument('name');
        $tenantName = $this->argument('tenantName');
        $password = $this->argument('password');
        $isSuperAdmin = (bool) $this->option('superAdmin') ?? false;
        $isTenantAdmin = (bool) $this->option('tenantAdmin') ?? false;
        $isVerified = (bool) $this->option('verified') ?? false;

        $user = User::where('email', $email)->first();
        if (!$user) {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'is_super_admin' => $isSuperAdmin,
                'email_verified_at' => $isVerified ? now() : null,
            ]);
            echo sprintf("User %s created\n", $email);
        } else {
            echo sprintf("User %s already exists\n", $email);
        }

        $tenant = Tenant::whereRaw('UPPER(name) LIKE ?', ['%' . strtoupper($tenantName) . '%'])->first();
        if (!$tenant) {
            $tenant = Tenant::create([
                'name' => $tenantName,
                'registration_type' => Tenant::REGISTRATION_TYPE_PUBLIC_DIRECT,
            ]);
            echo sprintf(   "Tenant %s created\n", $tenantName);
        } else {
            echo sprintf("Tenant %s already exists\n", $tenantName);
        }

        $tenantUser = TenantUser::findOneForUserAndTenant($user->id, $tenant->id);
        if (!$tenantUser) {
            $tenantUser = TenantUser::create([
                'user_id' => $user->id,
                'tenant_id' => $tenant->id,
                'is_active_on_tenant' => true,
            ]);
            echo "TenantUser created\n";
        } else {
            echo "TenantUser already exists\n";
        }

        if ($isTenantAdmin) {
            setPermissionsTeamId($tenant->id);
            if (!$user->hasRole('admin')) {
                $user->assignRole('admin');
                echo "admin-role added to User\n";
            }
        }

    }
}
