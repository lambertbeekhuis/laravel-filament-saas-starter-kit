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

class Client extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'name',
        'is_active',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'client_users')
            ->withPivot('is_active', 'is_admin', 'last_login_at', 'created_at')
            ->as('client_user');
    }

    public function clientUsers(): HasMany
    {
        return $this->hasMany(ClientUser::class);
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
}
