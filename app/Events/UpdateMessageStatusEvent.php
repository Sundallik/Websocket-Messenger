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

class UpdateMessageStatusEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private int $chatId;
    private Message $message;
    private int $userId;

    /**
     * Create a new event instance.
     */
    public function __construct(int $chatId, int $userId)
    {
        //
        $this->chatId = $chatId;
        $this->userId = $userId;
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
        return 'update_message_status';
    }

    public function broadcastWith(): array {
        return [
            'chat_id' => $this->chatId,
            'user_id' => $this->userId
        ];
    }
}
