<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AppController extends Controller
{
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
