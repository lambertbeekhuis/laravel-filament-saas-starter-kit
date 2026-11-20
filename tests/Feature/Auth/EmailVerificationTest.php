<?php

namespace Tests\Feature\Auth;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function userWithTenant(bool $verified = true): array
    {
        $tenant = Tenant::query()->create([
            'name' => 'Verify Org',
            'slug' => 'verify-org',
            'is_active' => true,
            'registration_type' => Tenant::REGISTRATION_TYPE_PUBLIC_DIRECT,
        ]);

        $user = User::factory()->create([
            'email_verified_at' => $verified ? now() : null,
            'is_active' => true,
        ]);

        $user->tenants()->attach($tenant->id, [
            'is_active_on_tenant' => true,
            'last_login_at' => now(),
        ]);

        return [$user, $tenant];
    }

    public function test_email_verification_screen_can_be_rendered(): void
    {
        [$user] = $this->userWithTenant(verified: false);

        $response = $this->actingAs($user)->get(route('verification.notice'));

        $response->assertOk();
    }

    public function test_email_can_be_verified(): void
    {
        [$user, $tenant] = $this->userWithTenant(verified: false);

        Event::fake();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        Event::assertDispatched(Verified::class);
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $response->assertRedirect(route('dashboard', ['tenant' => $tenant->slug], absolute: false).'?verified=1');
    }

    public function test_email_is_not_verified_with_invalid_hash(): void
    {
        [$user] = $this->userWithTenant(verified: false);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1('wrong-email')]
        );

        $this->actingAs($user)->get($verificationUrl);

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }
}
