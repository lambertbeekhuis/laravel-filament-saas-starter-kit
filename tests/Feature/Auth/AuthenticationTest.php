<?php

namespace Tests\Feature\Auth;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function userWithTenant(): array
    {
        $tenant = Tenant::query()->create([
            'name' => 'Test Org',
            'slug' => 'test-org',
            'is_active' => true,
            'registration_type' => Tenant::REGISTRATION_TYPE_PUBLIC_DIRECT,
        ]);

        $user = User::factory()->create([
            'is_active' => true,
        ]);

        $user->tenants()->attach($tenant->id, [
            'is_active_on_tenant' => true,
            'last_login_at' => now(),
        ]);

        return [$user, $tenant];
    }

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        [$user, $tenant] = $this->userWithTenant();

        $response = $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
            'tenant' => $tenant->slug,
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('dashboard', ['tenant' => $tenant->slug], absolute: false));

        $this->assertAuthenticated();
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        [$user, $tenant] = $this->userWithTenant();

        $response = $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'wrong-password',
            'tenant' => $tenant->slug,
        ]);

        $response->assertSessionHasErrorsIn('email');

        $this->assertGuest();
    }

    public function test_navigation_menu_can_be_rendered(): void
    {
        [$user, $tenant] = $this->userWithTenant();

        $this->actingAs($user);

        session(['tenant' => $tenant->slug]);

        $response = $this->get(route('dashboard', ['tenant' => $tenant->slug]));

        $response
            ->assertOk()
            ->assertSeeVolt('layout.navigation');
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('logout'));

        $response->assertRedirect(route('home_all'));

        $this->assertGuest();
    }
}
