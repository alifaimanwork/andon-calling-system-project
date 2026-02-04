<?php

declare(strict_types=1);

namespace App\Extras\Payloads;

class PendingData extends DataPayload
{
    public int $production_line_id;
    public int $count;
    public function __construct(int $production_line_id, int $count)
    {
        $this->production_line_id = $production_line_id;
        $this->count = $count;
    }
}
