<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatRouletteEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $continent;
    public $userId;

    public function __construct($message, $continent, $userId)
    {
        $this->message = $message;
        $this->continent = $continent;
        $this->userId = $userId;
    }

    public function broadcastOn()
    {
        return new Channel('chat.'.$this->continent);
    }
}