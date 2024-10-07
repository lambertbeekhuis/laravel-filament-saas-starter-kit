<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
class ClientUser extends Model
{
    use HasFactory;

    protected $table = 'client_users';

    protected $fillable = [
        'client_id',
        'user_id',
        'last_login_at',
        'is_active_on_client',
        'is_admin_on_client',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function findOneForUserAndClient($userId, $clientId)
    {
        return static::where('user_id', $userId)
            ->where('client_id', $clientId)
            ->first();
    }

    /**
     * Update-query, without retrieving the model first
     * Does not update the updated_at column field either
     */
    public static function updateLastLoginForUserAndClient(int $userId, int $clientId): void
    {
        DB::update("UPDATE client_users SET last_login_at = NOW() WHERE user_id = ? AND client_id = ?", [$userId, $clientId]);
    }

}
