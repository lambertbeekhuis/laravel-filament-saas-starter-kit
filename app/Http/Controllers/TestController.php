<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientUser;
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
            case 'notification':
                // sent email with (breeze) template https://laraveldaily.com/post/laravel-breeze-user-name-auth-email-templates
                $user = User::find(1);
                $client = Client::find(1);
                $user->notify(new SentInvitationToUserNotification($user, $client));
                return view('welcome');
            case 'mail':
                Mail::to($user->email)->send(new TestMail());
                return view('welcome');
            case 'clientUser':
                $clientUserLast = $user->clientUsersLastLogin()->first();

                return view('welcome');



                break;

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
