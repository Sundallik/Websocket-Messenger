<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $last_message = $this->last_message;

        return [
            'id' => $this->id,
            'title' => $this->title,
            'users' => $this->users,
            'chat_with' => $this->title ? null : UserResource::make($this->chatWith)->resolve(),
            'is_group' => $this->is_group,
            'unread_messages_count' => $this->unread_messages_count,
            'last_message' => $last_message ? MessageResource::make($last_message)->resolve() : null,
        ];
    }
}
