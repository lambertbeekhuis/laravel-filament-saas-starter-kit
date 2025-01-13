<?php

namespace App\Models;

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
    use HasFactory, InteractsWithMedia;

    protected $table = 'tenants';

    protected $fillable = [
        'name',
        'is_active',
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

    public static function findOneForSlugOrId($slugOrId): ?Tenant
    {
        return static::where('id', $slugOrId)
     //       ->orWhere('slug', $slugOrId)
            ->where('is_active', true)
            ->first();
    }

}
