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

class WorkCenterDataUpdateEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $plantId;
    public string $plantUid;
    public string $workCenterUid;

    public $workCenter;
    public $production;
    public array $productionLines;


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
    
        /** @var \App\Models\Production $currentProduction */
        $currentProduction = $workCenter->currentProduction()->first();
        //cache size optmization
        unset($currentProduction->created_at);
        unset($currentProduction->updated_at);

        unset($workCenter->created_at);
        unset($workCenter->updated_at);

        unset($workCenter->plant->created_at);
        unset($workCenter->plant->updated_at);

        if ($currentProduction) {
            $this->production = $currentProduction->toArray();
            unset($this->production['created_at']);
            unset($this->production['updated_at']);

            if ($this->production['schedule_data'] && $this->production['schedule_data']['shift_data']) {
                unset($this->production['schedule_data']['shift_data']['created_at']);
                unset($this->production['schedule_data']['shift_data']['updated_at']);
            }
            $this->productionLines = $currentProduction->productionLines()->with('productionOrder')->get()->toArray();

            foreach ($this->productionLines as &$productionLine) {
                unset($productionLine['created_at']);
                unset($productionLine['updated_at']);

                if ($productionLine['production_order']) {
                    unset($productionLine['production_order']['created_at']);
                    unset($productionLine['production_order']['updated_at']);

                    unset($productionLine['production_order']['pps_filehash']);
                    unset($productionLine['production_order']['pps_factory']);
                    unset($productionLine['production_order']['plant_id']);
                    unset($productionLine['production_order']['part']);
                    unset($productionLine['production_order']['pps_filename']);
                }
                unset($productionLine);
            }
        } else {
            $this->production = null;
            $this->productionLines = [];
        }
        $this->workCenter = $workCenter->toArray();
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
        return 'terminal.data-updated';
    }
}
