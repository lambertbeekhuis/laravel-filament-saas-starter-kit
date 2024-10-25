<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;
use App\Models\Client;
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

    protected $tenant = null;

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
                    $clientUser = ClientUser::query()
                        ->where('user_id', $this->id)
                        ->where('client_id', $tenant_id)
                        ->where('is_active_on_client', true)
                        ->where('is_admin_on_client', true)
                        ->first();
                    return (boolean) $clientUser;
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
     * is_active and is_admin are pivot columns, accessed by client_user->is_active_on_client and client_user->is_admin_on_client
     */
    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(Client::class, 'client_users')
            // ->using(ClientUser::class)
            ->withPivot('is_active_on_client', 'is_admin_on_client', 'last_login_at', 'created_at')
            ->as('client_user');
    }

    /**
     * Access pivot columns as retrieved by above clients() relationship
     * Can be used in e.g. Filament
     */
    public function getClientUserPivotAttribute()
    {
        return $this->clients->first()->client_user;
    }


    public function clientUsers(): HasMany
    {
        return $this->hasMany(ClientUser::class);
    }


    /**
     * To retrieve related client for logged-in user $this->clientsLastLogin(session('tenant', null))->first();
     * Or shorter/better auth()->client();
     */
    public function clientsLastLogin(?int $tenant_id = null): BelongsToMany
    {
        return $this->clients()
            ->where('client_users.is_active_on_client', true)
            ->where('clients.is_active', true)
            ->when($tenant_id, function ($query, $tenant_id) {
                $query->where('clients.id', $tenant_id);
            })
            ->orderBy('last_login_at', 'desc');
    }

    public function clientUsersLastLogin(?int $tenant_id): HasMany
    {
        return $this->clientUsers()
            ->join('clients', 'client_users.client_id', '=', 'clients.id')
            ->where('clients.is_active', true)
            ->where('client_users.is_active_on_client', true)
            ->when($tenant_id, function ($query, $tenant_id) {
                $query->where('client_users.client_id', $tenant_id);
            })
            ->orderBy('last_login_at', 'desc');
    }


    public static function getUsersForClient(int $client_id): Collection
    {
        return User::query()
            ->join('client_users', 'users.id', '=', 'client_users.user_id')
            ->where('client_users.client_id', $client_id)
            ->where('client_users.is_active_on_client', true)
            ->where('users.is_active', true)
            ->addSelect('users.*')
            ->addSelect('client_users.is_admin_on_client')
            ->addSelect('client_users.last_login_at')
            ->get();
    }


    /**
     * This should not be here, but in Auth or something, but it's here for now
     */
    public function getClientFromSession(): ?Client
    {
        return $this->clientsLastLogin(session('tenant', null))->first();
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
