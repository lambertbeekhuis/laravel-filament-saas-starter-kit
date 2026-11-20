<?php

namespace Tests\Feature\Auth;

use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenant_registration_screen_can_be_rendered(): void
    {
        $tenant = Tenant::query()->create([
            'name' => 'Open Org',
            'slug' => 'open-org',
            'is_active' => true,
            'registration_type' => Tenant::REGISTRATION_TYPE_PUBLIC_DIRECT,
        ]);

        $response = $this->get(route('register', ['tenant' => $tenant->slug]));

        $response
            ->assertOk()
            ->assertSeeVolt('pages.auth.register');
    }

    public function test_new_users_can_register_for_tenant(): void
    {
        $tenant = Tenant::query()->create([
            'name' => 'Open Org',
            'slug' => 'open-org',
            'is_active' => true,
            'registration_type' => Tenant::REGISTRATION_TYPE_PUBLIC_DIRECT,
        ]);

        $component = Volt::test('pages.auth.register', ['tenant' => $tenant->slug])
            ->set('name', 'Test User')
            ->set('email', 'test@example.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password');

        $component->call('register');

        $component->assertRedirect(route('dashboard', ['tenant' => $tenant->id], absolute: false));

        $this->assertAuthenticated();
    }
}
