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

new #[Layout('layouts.auth')] class extends Component {

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
                session(['tenant' => $this->tenant->slug]);
                $this->redirect(route('dashboard', ['tenant' => $this->tenant->slug], false), navigate: true);
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

<div class="flex flex-col gap-6">
    <x-auth-header
        :title="__('Create an account')"
        :description="__('Register for :tenant', ['tenant' => $tenant->name])"
    />

    <form wire:submit="register" class="flex flex-col gap-6">
        <flux:input wire:model="name" :label="__('Name')" type="text" required autofocus autocomplete="name" :placeholder="__('Full name')" />

        <flux:input wire:model="email" :label="__('Email address')" type="email" required autocomplete="email" placeholder="email@example.com" />

        <flux:input
            wire:model="password"
            :label="__('Password')"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="__('Password')"
            passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
            viewable
        />

        <flux:input
            wire:model="password_confirmation"
            :label="__('Confirm password')"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="__('Confirm password')"
            viewable
        />

        <flux:button type="submit" variant="primary" class="w-full">
            {{ __('Register') }}
        </flux:button>
    </form>

    <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
        <span>{{ __('Already have an account?') }}</span>
        <flux:link :href="route('login', ['tenant' => $tenant->slug])" wire:navigate>{{ __('Log in') }}</flux:link>
    </div>
</div>
