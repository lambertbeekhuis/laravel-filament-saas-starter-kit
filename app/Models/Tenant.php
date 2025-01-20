<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\InteractsWithMedia;

class Tenant extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, Sluggable;

    protected $table = 'tenants';

    const REGISTRATION_TYPE_PUBLIC_DIRECT = 'public_direct';
    const REGISTRATION_TYPE_PUBLIC_APPROVE = 'public_approve';
    const REGISTRATION_TYPE_INVITE_PERSONAL = 'invite_personal';
    const REGISTRATION_TYPE_INVITE_SECRET_LINK = 'invite_secret_link';

    protected $fillable = [
        'name',
        'is_active',
        'registration_type',
        'slug',
        'address',
        'zip',
        'city',
        'country',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'tenant_users')
            ->withPivot('is_active_on_tenant', 'is_admin_on_tenant', 'last_login_at', 'created_at')
            ->as('tenant_user');
    }


    /**
     * Utility method for when $user->tenants with pivot-data is used: to get the pivot data for is_admin_on_tenant
     * E.g. auth()->tenant()->relatedUserIsTenantAdmin()
     */
    public function relatedUserIsTenantAdmin(): bool
    {
        return (bool) $this->tenant_user?->is_admin_on_tenant;
    }

    public function tenantUsers(): HasMany
    {
        return $this->hasMany(TenantUser::class);
    }

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

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public static function getRegistrationTypes(): array
    {
        return [
            self::REGISTRATION_TYPE_PUBLIC_DIRECT => 'Public, direct access',
            self::REGISTRATION_TYPE_PUBLIC_APPROVE => 'Public, admin approval',
            self::REGISTRATION_TYPE_INVITE_PERSONAL => 'Personal Invite',
            self::REGISTRATION_TYPE_INVITE_SECRET_LINK => 'Secret Link Invite',
        ];
    }

    public static function findOneForSlugOrId($slugOrId): ?Tenant
    {
        return static::where(is_numeric($slugOrId) ? 'id' : 'slug', $slugOrId)
            ->where('is_active', true)
            ->first();
    }

    public function getLogoUrl(string $conversionName = 'thumb', $orAvatar = false): ?string
    {
        $url = $this->getFirstMediaUrl('logo', $conversionName);
        return $url ? $url : ($orAvatar ? 'https://www.gravatar.com/avatar/' . md5(strtolower(trim($this->name))) . '?d=mp' : null);
    }

}
