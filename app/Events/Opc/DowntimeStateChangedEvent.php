<?php

namespace App\Events\Opc;

use App\Models\CounterLog;
use App\Models\DowntimeStateLog;
use App\Models\ProductionLine;
use App\Models\WorkCenter;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DowntimeStateChangedEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $plantId;

    public $plantUid;
    public $workCenterUid;

    public $downtimeStateLog;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(WorkCenter $workCenter, DowntimeStateLog $downtimeStateLog)
    {
        $this->plantId = $workCenter->plant->id;
        $this->plantUid = $workCenter->plant->uid;

        $this->workCenterUid = $workCenter->uid;


        $this->downtimeStateLog = $downtimeStateLog->toArray();
    }

    // /**
    //  * The event's broadcast name.
    //  *
    //  * @return string
    //  */
    // public function broadcastAs()
    // {
    //     return 'terminal.downtime-state-changed';
    // }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('terminal.' . $this->plantUid . '.' . $this->workCenterUid);
    }
}
