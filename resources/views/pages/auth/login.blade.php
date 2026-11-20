<x-layouts::auth :title="__('Log in')">
    <div class="flex flex-col gap-6">
        <x-auth-header
            :title="__('Log in to your account')"
            :description="$tenantSlug
                ? __('Enter your email and password below to log in to :tenant', ['tenant' => $tenantSlug])
                : __('Enter your email and password below to log in')"
        />

        <x-auth-session-status class="text-center" :status="session('status')" />

        <x-passkey-verify />

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
            @csrf

            @if ($tenantSlug)
                <input type="hidden" name="tenant" value="{{ $tenantSlug }}">
            @endif

            <flux:input
                name="email"
                :label="__('Email address')"
                :value="old('email')"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="email@example.com"
            />

            <div class="relative">
                <flux:input
                    name="password"
                    :label="__('Password')"
                    type="password"
                    required
                    autocomplete="current-password"
                    :placeholder="__('Password')"
                    viewable
                />

                @if (Route::has('password.request'))
                    <flux:link class="absolute top-0 text-sm end-0" :href="route('password.request')" wire:navigate>
                        {{ __('Forgot your password?') }}
                    </flux:link>
                @endif
            </div>

            <flux:checkbox name="remember" :label="__('Remember me')" :checked="old('remember')" />

            <div class="flex items-center justify-end">
                <flux:button variant="primary" type="submit" class="w-full" data-test="login-button">
                    {{ __('Log in') }}
                </flux:button>
            </div>
        </form>

        <div class="space-x-1 text-sm text-center rtl:space-x-reverse text-zinc-600 dark:text-zinc-400">
            @if (Route::has('register.tenant'))
                <span>{{ __('Need an organization?') }}</span>
                <flux:link :href="route('register.tenant')" data-test="register-tenant-link" wire:navigate>
                    {{ __('Create one') }}
                </flux:link>
            @endif
        </div>
    </div>
</x-layouts::auth>