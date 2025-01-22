<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

/**
 * @todo should be improved with just login or other links
 */
new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" wire:navigate>
                        @if ($url = request()->tenant?->getLogoUrl('thumb', true))
                            <img src="{{ $url }}" alt="Logo" class="block h-16 w-auto fill-current text-gray-800 dark:text-gray-200"/>
                        @else
                            <x-application-logo class="block h-16 w-auto fill-current text-gray-800 dark:text-gray-200" />
                        @endif
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('home_tenant', ['tenant' => request()->tenant?->slug])" :active="request()->routeIs('home_tenant')" wire:navigate>
                        Home
                    </x-nav-link>
                </div>
            </div>

            <div class="">
                @if (auth()->user())
                    <x-nav-link :href="route('dashboard', ['tenant' => request()->tenant?->slug])" :active="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </x-nav-link>
                @else
                    <x-nav-link class="" :href="route('login', ['tenant' => request()->tenant?->slug])" wire:navigate class="bg-blue-500 hover:bg-blue-700 text-white font-bold mt-4 py-2 px-6 rounded">
                        {{ __('Login') }}
                    </x-nav-link>
                @endif
            </div>

        </div>
    </div>
</nav>
