<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
class TenantUser extends Model
{
    use HasFactory;

    protected $table = 'tenant_users';

    protected $fillable = [
        'tenant_id',
        'user_id',
        'last_login_at',
        'is_active_on_tenant',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function findOneForUserAndTenant($userId, $tenantId)
    {
        return static::where('user_id', $userId)
            ->where('tenant_id', $tenantId)
            ->first();
    }

    /**
     * Update-query, without retrieving the model first
     * Does not update the updated_at column field either
     */
    public static function updateLastLoginForUserAndTenant(int $userId, int $tenantId): void
    {
        DB::update("UPDATE tenant_users SET last_login_at = NOW() WHERE user_id = ? AND tenant_id = ?", [$userId, $tenantId]);
    }

}
