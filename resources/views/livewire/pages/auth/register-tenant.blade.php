<?php

use App\Actions\RegisterUserWithNewTenant;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
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

        $this->redirect(
            route('dashboard', ['tenant' => $result['tenant']->id], absolute: false),
            navigate: true,
        );
    }
}; ?>

<div>
    <div class="mb-6">
        <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
            {{ __('Create your account') }}
        </h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Register as a new user and create a new tenant organization.') }}
        </p>
    </div>

    <form wire:submit="registerTenant">
        <div>
            <x-input-label for="tenant_name" :value="__('Organization name')"/>
            <x-text-input wire:model="tenant_name" id="tenant_name" class="block mt-1 w-full" type="text"
                          name="tenant_name" required autofocus autocomplete="organization"/>
            <x-input-error :messages="$errors->get('tenant_name')" class="mt-2"/>
        </div>

        <div class="mt-4">
            <x-input-label for="name" :value="__('Your name')"/>
            <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" name="name" required
                          autocomplete="name"/>
            <x-input-error :messages="$errors->get('name')" class="mt-2"/>
        </div>

        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')"/>
            <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" name="email" required
                          autocomplete="username"/>
            <x-input-error :messages="$errors->get('email')" class="mt-2"/>
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')"/>

            <x-text-input wire:model="password" id="password" class="block mt-1 w-full"
                          type="password"
                          name="password"
                          required autocomplete="new-password"/>

            <x-input-error :messages="$errors->get('password')" class="mt-2"/>
        </div>

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
                {{ __('Create account') }}
            </x-primary-button>
        </div>
    </form>
</div>
