<?php

namespace App\Events\Terminal;

use App\Models\Plant;
use App\Models\ProductionLine;
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

class WorkCenterReworkUpdateEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $plantId;
    public string $plantUid;
    public string $workCenterUid;

    public $productionLine;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(WorkCenter $workCenter,ProductionLine $productionLine)
    {
        $this->plantId = $workCenter->plant->id;
        $this->plantUid = $workCenter->plant->uid;
        $this->workCenterUid = $workCenter->uid;
        $productionLine->production_order = $productionLine->productionOrder; //touch
        $this->productionLine = $productionLine->toArray();
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
        return 'terminal.rework-updated';
    }
}
