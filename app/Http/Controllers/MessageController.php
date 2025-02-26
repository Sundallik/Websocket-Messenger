<?php

namespace App\Http\Controllers;

use App\Events\StoreMessageEvent;
use App\Events\StoreMessageStatusEvent;
use App\Http\Requests\Message\StoreRequest;
use App\Http\Resources\MessageResource;
use App\Models\Chat;
use App\Models\Message;
use App\Models\MessageStatus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $authId = auth()->id();

        try {
            DB::beginTransaction();
            $message = Message::create([
                'user_id' => $authId,
                'chat_id' => $data['chat_id'],
                'text' => $data['text'],
            ]);
            $userIds = Chat::where('id', $data['chat_id'])->first()->chatUsers->pluck('id');
            foreach ($userIds as $userId) {
                if ($userId === $authId) continue;
                MessageStatus::create([
                    'chat_id' => $data['chat_id'],
                    'message_id' => $message->id,
                    'message_owner_id' => $message->user_id,
                    'user_id' => $userId,
                    'is_read' => false,
                ]);
                broadcast(new StoreMessageStatusEvent($userId, $data['chat_id'], $message))->toOthers();
            }
            DB::commit();
            broadcast(new StoreMessageEvent($data['chat_id'], $message))->toOthers();
        } catch (\Exception $e) {
            DB::rollBack();
            return response(['error' => $e->getMessage()], 500);
        }

        return MessageResource::make($message)->resolve();
    }
}
