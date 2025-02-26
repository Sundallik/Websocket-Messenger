<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageBroadcastResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => UserResource::make($this->user)->resolve(),
            'chat_id' => $this->chat_id,
            'text' => $this->text,
            'time' => $this->created_at->format('H:i'),
            'is_owner' => false
        ];
    }
}
