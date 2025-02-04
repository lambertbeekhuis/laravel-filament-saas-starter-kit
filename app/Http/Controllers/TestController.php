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

        $result = false;
        $imageUrl = null;

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

                $photos = $user->getMedia('profile');

                $user->clearMediaCollection('profile');

                $result = $user
                    ->addMediaFromUrl('https://www.gong-galaxy.com/cdn/shop/files/1500-T-24-KITE-RANGE-VERTIGO-9M-NAVY-GONGKITE-01_61d9a0e9-3a06-4176-b97b-657c968fa944.png')
                    ->toMediaCollection('profile');


                $photos = $user->getMedia('profile');

                $result = $user->getProfilePhotoUrl('thumb', true);
                $imageUrl = $user->getProfilePhotoUrl('thumb', true);

                $test = 1;

                break;
            case 'notification':
                // sent email with (breeze) template https://laraveldaily.com/post/laravel-breeze-user-name-auth-email-templates
                $user = User::find(1);
                $tenant = Tenant::find(1);
                $user->notify(new SentInvitationToUserNotification($user, $tenant));
                return view('welcome');
            case 'mail':
                $result = Mail::to($user->email)->send(new TestMail());
                break;
            case 'tenantUser':
                $result = $user->tenantUsersLastLogin()->first();
                break;
            case 'tenant':
                $user = User::find(1);
                //$tenantUsers1 = $user->tenantUsers;
                //$tenantUsers = TenantUser::all();
                $tenants = $user->tenants;
                $tenantFirst = $tenants->first();
                $tenantUserData = $tenantFirst->tenant_user;

                $tenantUserIsActive = $tenantUserData->is_active_on_tenant;


                dd($tenants, $tenantUserData, $tenantUserIsActive);



                $result = $user->tenants;


                break;
        }

        return view('testView', ['result' => $result, 'imageUrl' => $imageUrl]);

        return response()->json([
            'type' => $type,
            'result' => $result ?? null,
            'message' => 'Hello from TestController',
        ]);
    }
}
