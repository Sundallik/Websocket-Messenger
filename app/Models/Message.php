<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = 'messages';
    protected $guarded = [];

    protected $with = ['user'];
    protected $withCount = ['isReadStatuses'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getIsOwnerAttribute()
    {
        return (int)$this->user_id === (int)auth()->id();
    }

    public function isReadStatuses() {
        return $this->hasMany(MessageStatus::class, 'message_id', 'id')
            ->where('is_read', true);
    }
}
