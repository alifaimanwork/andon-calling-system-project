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

class WorkCenterDowntimeStateChangeEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $plantId;
    public string $plantUid;
    public string $workCenterUid;

    public array $workCenterDowntimes;

    public array $activeDowntimeEvents;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(WorkCenter $workCenter)
    {
        $this->plantId = $workCenter->plant->id;
        $this->plantUid = $workCenter->plant->uid;
        $this->workCenterUid = $workCenter->uid;

        $this->workCenterDowntimes = $workCenter->workCenterDowntimes()->get()->toArray();

        /** @var \App\Models\Production $production */
        $production = $workCenter->currentProduction;

        if (!$production) {
            $this->activeDowntimeEvents = [];
        } else {
            $this->activeDowntimeEvents = $production->getActiveDowntimeEvent();
        }
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('terminal.' . $this->plantUid . '.' . $this->workCenterUid);
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'terminal.downtime-state-changed';
    }
}
