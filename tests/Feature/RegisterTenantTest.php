<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use App\Support\Features;
use Database\Seeders\AdminRolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class RegisterTenantTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        if (Features::permissionsEnabled()) {
            $this->seed(AdminRolesAndPermissionsSeeder::class);
        }
    }

    public function test_register_tenant_screen_can_be_rendered(): void
    {
        $response = $this->get('/register-tenant');

        $response
            ->assertOk()
            ->assertSeeVolt('pages.auth.register-tenant');
    }

    public function test_new_user_can_register_with_new_tenant(): void
    {
        $component = Volt::test('pages.auth.register-tenant')
            ->set('tenant_name', 'Acme Corp')
            ->set('name', 'Jane Founder')
            ->set('email', 'founder@example.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password');

        $component->call('registerTenant');

        $tenant = Tenant::query()->where('name', 'Acme Corp')->first();

        $this->assertNotNull($tenant);
        $this->assertDatabaseHas('users', ['email' => 'founder@example.com']);
        $this->assertDatabaseHas('tenant_users', [
            'tenant_id' => $tenant->id,
            'is_active_on_tenant' => true,
        ]);

        $component->assertRedirect(route('dashboard', ['tenant' => $tenant->id], absolute: false));

        $this->assertAuthenticated();

        $user = User::query()->where('email', 'founder@example.com')->first();

        if (Features::permissionsEnabled()) {
            setPermissionsTeamId($tenant->id);
            $this->assertTrue($user->hasRole('admin'));
        }
    }

    public function test_tenant_name_must_be_unique(): void
    {
        Tenant::query()->create([
            'name' => 'Taken Name',
            'registration_type' => Tenant::REGISTRATION_TYPE_PUBLIC_DIRECT,
        ]);

        Volt::test('pages.auth.register-tenant')
            ->set('tenant_name', 'Taken Name')
            ->set('name', 'Jane Founder')
            ->set('email', 'founder@example.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->call('registerTenant')
            ->assertHasErrors(['tenant_name']);
    }
}
