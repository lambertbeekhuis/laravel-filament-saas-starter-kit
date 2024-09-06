<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'is_active',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->using(ClientUser::class)
            ->withPivot('is_active')
            ->as('client_user');
    }
}
