<?php

declare(strict_types=1);

namespace App\Extras\Payloads;

class DowntimeReasonData extends DataPayload
{
    public int $production_id;
    public int $downtime_id;
    public int $downtime_reason_id;
    public int|null $user_id;
    public string|null $user_input_reason;
    public function __construct(int $productionId, int $downtimeId, int $downtimeReasonId, string $userInputReason = null, int $userId = null)
    {
        $this->production_id = $productionId;
        $this->downtime_id = $downtimeId;
        $this->user_input_reason = $userInputReason;
        $this->downtime_reason_id = $downtimeReasonId;
        $this->user_id = $userId;
    }
}
