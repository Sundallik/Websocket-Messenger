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

class StoreMessageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private int $chatId;
    private Message $message;

    /**
     * Create a new event instance.
     */
    public function __construct(int $chatId, Message $message)
    {
        //
        $this->chatId = $chatId;
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('chat_' . $this->chatId),
        ];
    }

    public function broadcastAs(): string {
        return 'store_message';
    }

    public function broadcastWith(): array {
        return [
            'message' => MessageBroadcastResource::make($this->message)->resolve()
        ];
    }
}
