<?php

declare(strict_types=1);

namespace App\Extras\Payloads;

class DowntimeData extends DataPayload
{
    public int $work_center_downtime_id;
    public int $downtime_id;
    public int $set_state;
    public function __construct(int $work_center_downtime_id, int $downtime_id, int $set_state)
    {
        $this->work_center_downtime_id = $work_center_downtime_id;
        $this->downtime_id = $downtime_id;
        $this->set_state = $set_state;
        
    }
}
