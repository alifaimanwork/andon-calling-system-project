<?php

namespace App\Events\Terminal;

use App\Models\ActivityLog;
use App\Models\Production;
use App\Models\User;
use App\Models\WorkCenter;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StartProductionEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $plantId;
    public string $plantUid;
    public string $workCenterUid;
    public int $userId;

    public $production;
    public $productionOrders;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(WorkCenter $workCenter, Production $production, User $user)
    {
        $this->plantId = $workCenter->plant->id;
        $this->plantUid = $workCenter->plant->uid;
        $this->workCenterUid = $workCenter->uid;
        $this->userId = $user->id;

        $this->production = $production->toArray();
        $this->productionOrders = [];
        foreach ($production->productionOrders as $productionOrder) {
            $this->productionOrders[] = $productionOrder->order_no;
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
        return 'terminal.start-production';
    }


    public function generateActivityLog(): ActivityLog
    {
        $user = User::find($this->userId);

        return new ActivityLog(
            [
                'user_id' => $this->userId,
                'plant_id' => $this->plantId,
                'event_type' => 'terminal:operation',
                'event_title' => 'Start Production',
                'event_data' => json_encode([
                    'user' => $user->getCompactInfo(),
                    'work_center_uid' => $this->workCenterUid,
                    'production' => $this->production,
                    'production_orders' => $this->productionOrders
                ])
            ]
        );
    }
}
