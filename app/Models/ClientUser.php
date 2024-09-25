<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

    public static function findForUserAndClient($userId, $clientId)
    {
        return static::where('user_id', $userId)
            ->where('client_id', $clientId)
            ->first();
    }


}
