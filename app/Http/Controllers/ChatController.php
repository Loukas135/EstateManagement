<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;


class ChatController extends Controller
{

    public function getUserChats(Request $request)
    {
        $user = $request->user('sanctum');
        $chats = Chat::where('userOne', $user->id)
            ->orWhere('userTwo', $user->id)
            ->with('userOne', 'userTwo')
            ->get()->map(function ($chat) use ($user) {
                if ($chat->userOne === $user->id) {
                    return [
                        'chat_id' => $chat->id,
                        'user' => User::find($chat->userTwo),
                    ];
                } else {
                    return [
                        'chat_id' => $chat->id,
                        'user' => User::find($chat->userOne),
                    ];
                }
            });
        return response()->json(["chats" => $chats]);
        // if ($user->type === "Seller") {

        //     $chats = Chat::where('seller_id', $user->id)
        //         ->with('customer')
        //         ->get()
        //         ->map(function ($chat) {
        //             return [
        //                 'chat_id' => $chat->id,
        //                 'user' => $chat->customer
        //             ];
        //         });

        //     return response()->json(["chats" => $chats]);
        // } else {
        //     $chats = Chat::where('customer_id', $user->id)
        //         ->with('seller')
        //         ->get()
        //         ->map(function ($chat) {
        //             return [
        //                 'chat_id' => $chat->id,
        //                 'user' => $chat->seller
        //             ];
        //         });

        //     return response()->json(["chats" => $chats]);
        // }
    }

    public function getOrCreateChat(Request $request, $userId)
    {
        $currentUser = $request->user('sanctum');
        $chat = Chat::where(function ($query) use ($userId, $currentUser) {
            $query->where('userOne', $currentUser->id)
                ->where('userTwo', $userId);
        })->orWhere(function ($query) use ($userId, $currentUser) {
            $query->where('userOne', $userId)
                ->where('userTwo', $currentUser->id);
        })->first();

        if (!$chat) {
            $chat = Chat::create([
                'userOne' =>  $userId,
                'userTwo' => $currentUser->id,
            ]);
        }

        if ($currentUser->id === $chat->userOne) {
            $user = User::find($chat->userTwo);
        } else {
            $user = User::find($chat->userOne);
        }

        $response = [
            'chat_id' => $chat->id,
            'user' => $user
        ];

        return response()->json($response);
    }


    public function sendMessage(Request $request)
    {
        $currentUser = $request->user('sanctum');
        $message = Message::create([
            'chat_id' => $request->chat_id,
            'sender_id' => $currentUser->id,
            'message' => $request->message,
        ]);
        $createdMessage = Message::with('sender')->find($message->id);

        event(new MessageSent($createdMessage));

        return response()->json(['message' => $createdMessage]);
    }

    public function fetchMessages($chatId)
    {
        $messages = Message::with('sender')->where('chat_id', $chatId)->get();

        return response()->json(["messages" => $messages]);
    }
}
