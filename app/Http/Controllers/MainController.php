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

        $validated = $request->validate([
            'message' => 'nullable|string|max:1000',
            'file' => [
                'nullable',
                'file',
                'mimes:jpeg,png,jpg,gif,mp4,mov,avi,pdf,docx,xlsx,zip',
                'max:10240',
            ],
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
    
            if (in_array($file->extension(), ['jpeg', 'png', 'jpg', 'gif'])) {
                $folder = 'images';
            } elseif (in_array($file->extension(), ['mp4', 'mov', 'avi'])) {
                $folder = 'videos';
            } else {
                $folder = 'files';
            }
    
            $file_name = time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
    
            $destinationPath = public_path($folder);
    
            $file->move($destinationPath, $file_name);
    
            $filePath = $folder . '/' . $file_name;

            $validated['file'] = $filePath;
        }

        $message = Messages::create([
            'chat_id' => $chat_id,
            'message' => $request->message,
            'file' => $filePath,
        ]);

        broadcast(new MessageSent($message))->toOthers();
        
        return back();
    }
}
