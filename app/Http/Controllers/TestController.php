<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\TenantUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestMail;
use App\Notifications\SentInvitationToUserNotification;

class TestController extends Controller
{

    /**
     * Test route for Spatie Permissions
     */
    public function testPermissions(Request $request)
    {
        $user = Auth::user();

        $tenant = $request->tenant;

        return view('testView', ['result' => ['tenant' => $tenant]]);
    }


    /**
     * /test/{type}
     */
    public function test(Request $request, $type)
    {
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            return view('testView', ['result' => 'No permission']);
        }

        switch ($type) {
            case 'roles':
                setPermissionsTeamId(1); // is needed for a non-tenant route
                $roles = $user->roles;
                $role = $roles->first();

                $permissions = $user->permissions;
                $permission = $permissions->first();

                return view('testView', ['result' => ['role' => $role, 'permission' => $permission]]);
                break;
            case 'lastlogin':
                $tenant = $user->tenantsLastLogin()->first();
                $tenant_id = $tenant->tenant_user->tenant_id;
                TenantUser::updateLastLoginForUserAndTenant($user->id, $tenant_id);

                return view('welcome');
            case 'photo':
                $user->addMediaFromUrl('https://www.gong-galaxy.com/cdn/shop/files/1500-T-24-KITE-RANGE-VERTIGO-9M-NAVY-GONGKITE-01_61d9a0e9-3a06-4176-b97b-657c968fa944.png')
                    ->toMediaCollection('profile');



            case 'notification':
                // sent email with (breeze) template https://laraveldaily.com/post/laravel-breeze-user-name-auth-email-templates
                $user = User::find(1);
                $tenant = Tenant::find(1);
                $user->notify(new SentInvitationToUserNotification($user, $tenant));
                return view('welcome');
            case 'mail':
                Mail::to($user->email)->send(new TestMail());
                return view('welcome');
            case 'tenantUser':
                $tenantUserLast = $user->tenantUsersLastLogin()->first();

                return view('welcome');
                break;
            case 'tenant':
                $user = User::find(1);
                //$tenantUsers1 = $user->tenantUsers;
                //$tenantUsers = TenantUser::all();
                $tenants = $user->tenants;
                $tenantFirst = $tenants->first();
                $tenantUserData = $tenantFirst->tenant_user;

                $tenantUserIsActive = $tenantUserData->is_active_on_tenant;
                $tenantUserIsAdmin = $tenantUserData->is_admin_on_tenant;

                dd($tenants, $tenantUserData, $tenantUserIsAdmin, $tenantUserIsActive);



                $result = $user->tenants;


                break;
            default:
                $result = 'type not found';
        }

        return view('welcome');

        return response()->json([
            'type' => $type,
            'result' => $result ?? null,
            'message' => 'Hello from TestController',
        ]);
    }
}
