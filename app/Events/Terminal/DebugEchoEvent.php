<?php

namespace App\Events\Terminal;

use App\Models\Plant;
use App\Models\User;
use App\Models\WorkCenter;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DebugEchoEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $plantUid;
    public string $workCenterUid;
    public string $debugChannel;
    public array $data;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Plant $plant, WorkCenter $workCenter, $debugChannel, array $data)
    {

        $this->plantUid = $plant->uid;
        $this->workCenterUid = $workCenter->uid;
        $this->debugChannel = $debugChannel;
        $this->data = $data;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('debug.' . $this->plantUid . '.' . $this->workCenterUid . '.' . $this->debugChannel);
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'debug.' . $this->plantUid . '.' . $this->workCenterUid . '.' . $this->debugChannel;
    }
}
