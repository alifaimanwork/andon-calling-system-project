<?php

namespace App\Events\Terminal;

use App\Events\ActivityLogEvent;
use App\Models\ActivityLog;
use App\Models\Production;
use App\Models\ProductionLine;
use App\Models\User;
use App\Models\WorkCenter;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
//use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RejectSettingsUpdatedEvent implements ActivityLogEvent, ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $plantId;
    public string $plantUid;
    public string $workCenterUid;
    public int $userId;

    public $productionLine;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(WorkCenter $workCenter, ProductionLine $productionLine, User $user)
    {
        $this->plantId = $workCenter->plant->id;
        $this->plantUid = $workCenter->plant->uid;
        $this->workCenterUid = $workCenter->uid;
        $this->userId = $user->id;

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
        return 'terminal.reject-settings-updated';
    }

    public function generateActivityLog(): ActivityLog
    {
        $user = User::find($this->userId);

        return new ActivityLog(
            [
                'user_id' => $this->user->id ?? null,
                'plant_id' => $this->plantId,
                'event_type' => 'terminal:operation',
                'event_title' => 'Update reject settings',
                'event_data' => json_encode([
                    'user' => $user->getCompactInfo(),
                    'work_center_uid' => $this->workCenterUid,
                    'production_line' => $this->productionLine
                ])
            ]
        );
    }


}
