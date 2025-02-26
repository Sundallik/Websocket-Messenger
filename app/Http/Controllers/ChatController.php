<?php

namespace App\Http\Controllers;

use App\Events\StoreGroupChatEvent;
use App\Events\UpdateMessageStatusEvent;
use App\Http\Requests\Chat\StoreRequest;
use App\Http\Resources\ChatResource;
use App\Http\Resources\MessageResource;
use App\Http\Resources\UserResource;
use App\Models\Chat;
use App\Models\User;

class ChatController extends Controller
{
    public function index()
    {
        $users = User::whereNot('id', auth()->id())->get();
        $users = UserResource::collection($users)->resolve();

        $chats = auth()->user()->chats()
            ->with('messages')
            ->withCount('messages', 'unreadMessages')
            ->where(function($query) {
                $query->where('messages_count', '>', 0)->orWhere('is_group', true);
            })->get();

        $chats = ChatResource::collection($chats)->resolve();

        return inertia('Chat/Index', compact('users', 'chats'));
    }

    public function getChat(int $id)
    {
        $chat = Chat::where('id', $id)->withCount('unreadMessages')->first();
        return ChatResource::make($chat)->resolve();
    }

    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $data['users'][] = auth()->id();
        sort($data['users']);

        $usersIds = $data['users'];
        $usersIdsString = implode('-', $usersIds);

        if (!$data['title']) {
            $chat = Chat::firstOrCreate([
                'users' => $usersIdsString,
            ],[
                'users' => $usersIdsString,
            ]);
        } else {
            $chat = Chat::create([
                'title' => $data['title'],
                'users' => $usersIdsString,
                'is_group' => true
            ]);

            foreach ($usersIds as $userId) {
                if ($userId === auth()->id()) continue;
                broadcast(new StoreGroupChatEvent($userId, $chat->id))->toOthers();
            }
        }

        $chat->chatUsers()->sync($usersIds);

        return redirect()->route('chats.show', $chat->id);
    }

    public function show(Chat $chat)
    {
        $page = request()->query('page', 1);

        $res = $chat->messages()->latest()->paginate(5, '*', 'page', $page);
        $messages = MessageResource::collection($res)->resolve();

        $hasNext = (bool)$res->nextPageUrl();

        if ($page > 1) {
            return response()->json([
                'has_next' => $hasNext,
                'messages' => $messages,
            ]);
        }

        $users = $chat->chatUsers;

        $unreadOwnerIds = $chat->unreadMessages->pluck('message_owner_id')->unique();
        $chat->unreadMessages()->update(['is_read' => true]);

        foreach ($unreadOwnerIds as $userId) {
            broadcast(new UpdateMessageStatusEvent($chat->id, $userId))->toOthers();
        }

        $chat = ChatResource::make($chat)->resolve();

        return inertia('Chat/Show', compact('chat', 'messages', 'users', 'hasNext'));
    }
}
