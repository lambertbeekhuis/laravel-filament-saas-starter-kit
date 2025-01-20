<?php

use App\Models\User;
use App\Models\Tenant;
use App\Models\TenantUser;
use Illuminate\Auth\Events\Registered;
use App\Events\RegisteredTenantUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {

    protected ?string $token;
    public $tenant;

    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';


    public function mount(string $tenant): void
    {
        $this->tenant = Tenant::findOneForSlugOrId($tenant);
        $this->token = request()->token; // optional route-parameter

        if (in_array($this->tenant->registration_type, [Tenant::REGISTRATION_TYPE_INVITE_PERSONAL, Tenant::REGISTRATION_TYPE_INVITE_SECRET_LINK])) {
            if (!$this->token) {
                abort(403, 'Token required for registration, no token');
            }
            abort(403, 'No implemented yet');
        }
    }

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        DB::beginTransaction();
        try {
            $user = User::create($validated);
            $tenantUser = TenantUser::create([
                'tenant_id' => $this->tenant->id,
                'user_id' => $user->id,
                'is_active_on_tenant' => $this->tenant->registration_type === Tenant::REGISTRATION_TYPE_PUBLIC_DIRECT,
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        //event(new Registered($user));
        event(new RegisteredTenantUser($tenantUser)); // sends e.g. email

        switch ($this->tenant->registration_type) {
            case Tenant::REGISTRATION_TYPE_PUBLIC_DIRECT:
                Auth::login($user);
                $this->redirect(route('dashboard', ['tenant' => $this->tenant->id], false), navigate: true);
                break;
            case Tenant::REGISTRATION_TYPE_PUBLIC_APPROVE:
                // should be notified in the email
                //@todo add notification
                $this->redirect(route('home_all', ['tenant' => $this->tenant->id], false), navigate: true);
                break;
            case Tenant::REGISTRATION_TYPE_INVITE_PERSONAL:
                // not implemented yet

            case Tenant::REGISTRATION_TYPE_INVITE_SECRET_LINK:
                // not implemented yet
            default:
                throw new \Exception('Unknown registration type ' . $this->tenant->registration_type);
        }

    }
};
?>

<div>
    <div>Register for {{$tenant->name}}</div>

    <form wire:submit="register">
        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')"/>
            <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" name="name" required
                          autofocus autocomplete="name"/>
            <x-input-error :messages="$errors->get('name')" class="mt-2"/>
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')"/>
            <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" name="email" required
                          autocomplete="username"/>
            <x-input-error :messages="$errors->get('email')" class="mt-2"/>
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')"/>

            <x-text-input wire:model="password" id="password" class="block mt-1 w-full"
                          type="password"
                          name="password"
                          required autocomplete="new-password"/>

            <x-input-error :messages="$errors->get('password')" class="mt-2"/>
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')"/>

            <x-text-input wire:model="password_confirmation" id="password_confirmation" class="block mt-1 w-full"
                          type="password"
                          name="password_confirmation" required autocomplete="new-password"/>

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2"/>
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
               href="{{ route('login') }}" wire:navigate>
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</div>
