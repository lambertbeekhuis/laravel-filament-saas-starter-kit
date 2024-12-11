<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;
use App\Models\Tenant;
use Filament\Panel;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Collection;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Image\Enums\Fit;


class User extends Authenticatable implements FilamentUser, HasTenants, HasMedia
{
    use HasFactory, Notifiable, interactsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'last_name',
        'middle_name',
        'email',
        'password',
        'is_active',
        'is_super_admin',
        'date_of_birth',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $authTenant = false; // false indicates: not retrieved yet

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function teams(): BelongsToMany
    {
        return $this->tenants();

    }

    /**
     * For Filament admin panel https://github.com/filamentphp/filament/discussions/7668
     *
     */
    public function tenant(): BelongsToMany
    {
        return $this->tenants()->where('tenant_id', Filament::getTenant()->id);
    }

    public function getTenants(Panel $panel): Collection
    {
        return $this->tenants;
    }

    public function canAccessTenant(Model $tenant): bool
    {
        return $this->tenants()->whereKey($tenant)->exists();
    }

    public function canAccessPanel(Panel $panel): bool
    {
        switch ($panel->getId()) {
            case 'admin':
                if ($tenant_id = request()->route('tenant')) {
                    $tenantUser = TenantUser::query()
                        ->where('user_id', $this->id)
                        ->where('tenant_id', $tenant_id)
                        ->where('is_active_on_tenant', true)
                        ->where('is_admin_on_tenant', true)
                        ->first();
                    return (boolean) $tenantUser;
                }
                return true; // without tenant specified, you have access to /admin, which will be redirect to something with tenant
            case 'superadmin':
                return $this->isSuperAdmin();
        }
        return false;
    }

    // for Spatie/media-library
    public function registerMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('preview')
            ->fit(Fit::Contain, 300, 300)
            ->nonQueued();

        $this
            ->addMediaConversion('thumb')
            ->fit(Fit::Contain, 100, 100)
            ->nonQueued();

    }


    /**
     * is_active and is_admin are pivot columns, accessed by tenant_user->is_active_on_tenant and tenant_user->is_admin_on_tenant
     */
    public function tenants(): BelongsToMany
    {
        return $this->belongsToMany(Tenant::class, 'tenant_users')
            // ->using(TenantUser::class)
            ->withPivot('is_active_on_tenant', 'is_admin_on_tenant', 'last_login_at', 'created_at')
            ->as('tenant_user');
    }

    /**
     * Access pivot columns as retrieved by above tenants() relationship
     * Can be used in e.g. Filament
     */
    public function getTenantUserPivotAttribute()
    {
        return $this->tenants->first()->tenant_user;
    }


    public function tenantUsers(): HasMany
    {
        return $this->hasMany(TenantUser::class);
    }


    /**
     * To retrieve related tenant for logged-in user $this->tenantsLastLogin(session('tenant', null))->first();
     * Or shorter/better auth()->tenant();
     */
    public function tenantsLastLogin(?int $tenant_id = null): BelongsToMany
    {
        return $this->tenants()
            ->where('tenant_users.is_active_on_tenant', true)
            ->where('tenants.is_active', true)
            ->when($tenant_id, function ($query, $tenant_id) {
                $query->where('tenants.id', $tenant_id);
            })
            ->orderBy('last_login_at', 'desc');
    }

    public function tenantUsersLastLogin(?int $tenant_id): HasMany
    {
        return $this->tenantUsers()
            ->join('tenants', 'tenant_users.tenant_id', '=', 'tenants.id')
            ->where('tenants.is_active', true)
            ->where('tenant_users.is_active_on_tenant', true)
            ->when($tenant_id, function ($query, $tenant_id) {
                $query->where('tenant_users.tenant_id', $tenant_id);
            })
            ->orderBy('last_login_at', 'desc');
    }

    /**
     * Specific function for auth()->tenant() to retrieve the CACHED tenant for the )authenticated) user
     */
    public function authTenantForUser($tenant): ?Tenant
    {
        if ($this->authTenant === false) {
            $this->authTenant = $this->tenantsLastLogin($tenant)->first();
        }
        return $this->authTenant;
    }


    public static function getUsersForTenant(int $tenant_id): Collection
    {
        return User::query()
            ->join('tenant_users', 'users.id', '=', 'tenant_users.user_id')
            ->where('tenant_users.tenant_id', $tenant_id)
            ->where('tenant_users.is_active_on_tenant', true)
            ->where('users.is_active', true)
            ->addSelect('users.*')
            ->addSelect('tenant_users.is_admin_on_tenant')
            ->addSelect('tenant_users.last_login_at')
            ->get();
    }



    public function getFullNameAttribute()
    {
         return implode(' ', array_filter([$this->name, $this->middle_name, $this->last_name]));
    }

    public function getInitialsAttribute()
    {
        return $this->last_name ? strtoupper($this->name[0] . $this->last_name[0]) : strtoupper($this->name[0]) . $this->name[1] ?? '';
    }


    public function isSuperAdmin(): bool
    {
        return (bool) $this->is_super_admin;
    }


}
