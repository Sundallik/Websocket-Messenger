<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chat extends Model
{
    protected $table = 'chats';
    protected $guarded = [];

    public function chatUsers()
    {
        return $this->belongsToMany(User::class, 'chat_users', 'chat_id', 'user_id');
    }

    public function chatWith()
    {
        return $this->hasOneThrough(User::class, ChatUser::class, 'chat_id', 'id', 'id', 'user_id' )
            ->where('user_id', '!=', auth()->id());
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function unreadMessages()
    {
        return $this->hasMany(MessageStatus::class)
            ->where('message_statuses.user_id', '=', auth()->id())
            ->where('is_read', false);
    }

    public function getLastMessageAttribute()
    {
//        return $this->messages()->latest()->first();
        return $this->messages->pop();
    }
}
