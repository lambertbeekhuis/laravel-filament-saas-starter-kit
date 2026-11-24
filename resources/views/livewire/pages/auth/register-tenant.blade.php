<?php

use App\Actions\RegisterUserWithNewTenant;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.auth')] class extends Component
{
    public string $tenant_name = '';

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function registerTenant(RegisterUserWithNewTenant $registerUserWithNewTenant): void
    {
        $validated = $this->validate([
            'tenant_name' => ['required', 'string', 'max:255', 'unique:tenants,name'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $result = $registerUserWithNewTenant->register([
            'tenant_name' => $validated['tenant_name'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($result['user']);

        session(['tenant' => $result['tenant']->slug]);

        $this->redirect(
            route('dashboard', ['tenant' => $result['tenant']->slug], absolute: false),
            navigate: true,
        );
    }
}; ?>

<div class="flex flex-col gap-6">
        <x-auth-header
            :title="__('Create an account')"
            :description="__('Register and create a new organization (tenant).')"
        />

        <form wire:submit="registerTenant" class="flex flex-col gap-6">
            <flux:input
                wire:model="tenant_name"
                :label="__('Organization name')"
                type="text"
                required
                autofocus
                autocomplete="organization"
                :placeholder="__('Your company')"
            />

            <flux:input
                wire:model="name"
                :label="__('Your name')"
                type="text"
                required
                autocomplete="name"
                :placeholder="__('Full name')"
            />

            <flux:input
                wire:model="email"
                :label="__('Email address')"
                type="email"
                required
                autocomplete="email"
                placeholder="email@example.com"
            />

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

            <flux:button type="submit" variant="primary" class="w-full" data-test="register-tenant-button">
                {{ __('Create account') }}
            </flux:button>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
            <span>{{ __('Already have an account?') }}</span>
            <flux:link :href="route('login')" wire:navigate>{{ __('Log in') }}</flux:link>
        </div>
</div>