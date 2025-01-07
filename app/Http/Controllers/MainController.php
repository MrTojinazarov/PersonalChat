<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Chats;
use App\Models\Messages;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MainController extends Controller
{
    public function index()
    {
        $users = User::where('id', '!=', Auth::id())->get();

        return view('main.index', compact('users'));
    }

    public function chat($receiver_id)
    {

        $users = User::where('id', '!=', Auth::id())->get();

        $user = Auth::user();
        $receiver = User::findOrFail($receiver_id);
        $chat = Chats::where(function ($query) use ($user, $receiver) {
            $query->where('sender_id', $user->id)
                ->where('receiver_id', $receiver->id);
        })->orWhere(function ($query) use ($user, $receiver) {
            $query->where('sender_id', $receiver->id)
                ->where('receiver_id', $user->id);
        })->first();

        if (!$chat) {
            $chat = Chats::create([
                'sender_id' => $user->id,
                'receiver_id' => $receiver->id,
            ]);
        }

        $messages = Messages::where('chat_id', $chat->id)->get();

        return view('main.index', compact('chat', 'users', 'receiver', 'messages'));
    }

    public function sendMessage(Request $request, $chat_id)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $message = Messages::create([
            'chat_id' => $chat_id,
            'message' => $request->message,
        ]);

        // dd($message);
        broadcast(new MessageSent($message));
        
        return back();
    }
}
