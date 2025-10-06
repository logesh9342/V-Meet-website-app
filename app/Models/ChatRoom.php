<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatRoom extends Model
{
    protected $fillable = ['name', 'type'];

    public function participants()
    {
        return $this->belongsToMany(User::class, 'chat_room_user')->withTimestamps();
    }
}
