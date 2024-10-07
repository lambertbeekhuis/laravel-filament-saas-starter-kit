<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Client;
use App\Models\ClientUser;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;

class makeUser extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:make-user {email : login email of the user} {name : (first)name of the user} {clientName : company name of the user (=tenantName}  {--clientAdmin : is the user a ClientAdmin }  {--superAdmin : is the user a superAdmin}';

    /*
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make a new User and Client for the SaasSetup';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $name = $this->argument('name');
        $clientName = $this->argument('clientName');
        $isSuperAdmin = (bool) $this->option('superAdmin') ?? false;
        $isClientAdmin = (bool) $this->option('clientAdmin') ?? false;

        $user = User::where('email', $email)->first();
        if (!$user) {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => bcrypt('password'),
                'is_super_admin' => $isSuperAdmin,
            ]);
            echo sprintf("User %s created\n", $email);
        } else {
            echo sprintf("User %s exists\n", $email);
        }

        $client = Client::whereRaw('UPPER(name) LIKE ?', ['%' . strtoupper($clientName) . '%'])->first();
        if (!$client) {
            $client = Client::create([
                'name' => $clientName,
            ]);
            echo sprintf(   "Client %s created\n", $clientName);
        } else {
            echo sprintf("Client %s exists\n", $clientName);
        }

        $clientUser = ClientUser::findOneForUserAndClient($user->id, $client->id);
        if (!$clientUser) {
            $clientUser = ClientUser::create([
                'user_id' => $user->id,
                'client_id' => $client->id,
                'is_active_on_client' => true,
                'is_admin_on_client' => $isClientAdmin,
            ]);
            echo "ClientUser created\n";
        } else {
            echo "ClientUser exists\n";
        }


    }
}
