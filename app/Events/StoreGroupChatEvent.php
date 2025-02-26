<?php

namespace App\Events;

use App\Http\Resources\MessageBroadcastResource;
use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StoreGroupChatEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private int $userId;
    private int $chatId;

    /**
     * Create a new event instance.
     */
    public function __construct(int $userId, int $chatId)
    {
        $this->userId = $userId;
        $this->chatId = $chatId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('users_' . $this->userId),
        ];
    }

    public function broadcastAs(): string {
        return 'store_group_chat';
    }

    public function broadcastWith(): array {
        return [
            'chat_id' => $this->chatId,
        ];
    }
}
