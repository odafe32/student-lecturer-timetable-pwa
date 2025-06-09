<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PushSubscription extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'endpoint',
        'p256dh_key',
        'auth_token',
    ];

    /**
     * Get the user that owns the push subscription.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}