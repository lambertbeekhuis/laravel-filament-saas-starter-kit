<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;
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
        return $this->clients();

    }


    /**
     * For Filament admin panel https://github.com/filamentphp/filament/discussions/7668
     */
    public function client(): BelongsToMany
    {
        return $this->clients()->where('client_id', Filament::getTenant()->id);
    }

    public function getTenants(Panel $panel): Collection
    {
        return $this->clients;
    }

    public function canAccessTenant(Model $tenant): bool
    {
        return $this->clients()->whereKey($tenant)->exists();
    }

    public function canAccessPanel(Panel $panel): bool
    {
        switch ($panel->getId()) {
            case 'admin':
                if ($tenant_id = request()->route('tenant')) {
                    $clientUser = ClientUser::query()->where('user_id', $this->id)->where('client_id', $tenant_id)->first();
                    return ($clientUser && $clientUser->is_admin);
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
    }


    /**
     * is_active and is_admin are pivot columns, accessed by client_user->is_active and client_user->is_admin
     */
    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(Client::class, 'client_users')
            // ->using(ClientUser::class)
            ->withPivot('is_active', 'is_admin', 'created_at')
            ->as('client_user');
    }

    public function clientUsers(): HasMany
    {
        return $this->hasMany(ClientUser::class);
    }

    public function isSuperAdmin(): bool
    {
        return (bool)$this->is_super_admin;
    }


}
