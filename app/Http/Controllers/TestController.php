<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            case 'client':
                $user = User::find(1);
                //$clientUsers1 = $user->clientUsers;
                $clientUsers = ClientUser::all();
                $clients = $user->clients;
                // $clients = Client::all();

                dd($clientUsers, $clients);



                $result = $user->clients;


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
