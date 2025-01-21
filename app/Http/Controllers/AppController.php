<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AppController extends Controller
{

    /**
     * Public home page for a Tenant
     */
    public function home(Request $request)
    {
        $tenant = $request->tenant;

        return view('home', [
            'tenant' => $tenant,

        ]);
    }

    /**
     * Authenticated Dashboard for a Tenant
     */
    public function dashboard(Request $request)
    {
        $tenant = $request->tenant;

        $users = User::getUsersForTenant($tenant->id);

        return view('dashboard', [
            'tenant' => $tenant,
            'users' => $users,
        ]);
    }
}
