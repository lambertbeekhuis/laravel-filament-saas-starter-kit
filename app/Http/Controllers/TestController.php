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

    public function test(Request $request, $type)
    {
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            return response()->json([
                'type' => $type,
                'message' => 'You are not super admin',
            ]);
        }

        switch ($type) {
            case 'lastlogin':
                $tenant = $user->tenantsLastLogin()->first();
                $tenant_id = $tenant->tenant_user->tenant_id;
                TenantUser::updateLastLoginForUserAndTenant($user->id, $tenant_id);

                return view('welcome');
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

        return response()->json([
            'type' => $type,
            'result' => $result ?? null,
            'message' => 'Hello from TestController',
        ]);
    }
}
