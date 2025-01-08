<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct($message,)
    {
        $this->message = $message;
    }


    public function broadcastOn(): array
    {
        return [
            new Channel('personal_chat'),
        ];
    }

    public function broadcastWith()
    {
        return [
            'message' => $this->message, 
            'time' => now()->toDateTimeString(),
        ];
    }
}
