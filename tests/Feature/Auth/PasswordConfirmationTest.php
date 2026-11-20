<?php

namespace Tests\Feature\Auth;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PasswordConfirmationTest extends TestCase
{
    use RefreshDatabase;

    protected function userWithTenant(): array
    {
        $tenant = Tenant::query()->create([
            'name' => 'Confirm Org',
            'slug' => 'confirm-org',
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

    public function test_confirm_password_screen_can_be_rendered(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('password.confirm'));

        $response->assertOk();
    }

    public function test_password_can_be_confirmed(): void
    {
        [$user, $tenant] = $this->userWithTenant();

        $response = $this->actingAs($user)->post(route('password.confirm.store'), [
            'password' => 'password',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
    }

    public function test_password_is_not_confirmed_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('password.confirm.store'), [
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrorsIn('password');
    }
}
