<?php

declare(strict_types=1);

namespace App\Extras\Payloads;

class RejectData extends DataPayload
{
    public int $production_line_id;
    public int $reject_type_id;
    public int $count;
    public function __construct(int $production_line_id, int $reject_type_id, int $count)
    {
        $this->production_line_id = $production_line_id;
        $this->reject_type_id = $reject_type_id;
        $this->count = $count;
    }
}
