<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserOnlineStatus extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'socket_id', 'last_seen', 'is_online'];

    protected $casts = [
        'is_online' => 'boolean',
        'last_seen' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
