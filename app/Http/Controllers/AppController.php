<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AppController extends Controller
{
    public function dashboard(Request $request)
    {
        $client = $request->client;

        $users = User::getUsersForClient($client->id);


        return view('dashboard', [
            'client' => $client,
            'users' => $users,
        ]);
    }
}
