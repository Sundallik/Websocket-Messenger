<?php

namespace App\Http\Controllers;

use App\Events\UpdateMessageStatusEvent;
use App\Http\Requests\MessageStatus\UpdateRequest;
use App\Models\MessageStatus;
use Illuminate\Http\Request;

class MessageStatusController extends Controller
{
    public function update(UpdateRequest $request)
    {
        $data = $request->validated();

        $messageStatus = MessageStatus::where('user_id', auth()->id())
            ->where('message_id', $data['message_id'])->first();

        if (!$messageStatus->is_read) {
            $messageStatus->update(['is_read' => true]);
            broadcast(new UpdateMessageStatusEvent($messageStatus->chat_id, $messageStatus->message_owner_id))->toOthers();
        }
    }
}
