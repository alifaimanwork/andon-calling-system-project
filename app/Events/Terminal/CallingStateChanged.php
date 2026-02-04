<?php

namespace App\Events\Terminal;

use App\Models\Plant;
use App\Models\User;
use App\Models\WorkCenter;
use App\Models\Calling;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

//x guna pun this whole `Events` - simpan mgkn utk next usage
class CallingStateChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $state;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($state)
    {
        $this->state = $state;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('callings');
    }
}
