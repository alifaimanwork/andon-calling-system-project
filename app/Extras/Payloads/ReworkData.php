<?php

declare(strict_types=1);

namespace App\Extras\Payloads;

class ReworkData extends DataPayload
{
    public int $production_line_id;
    public int $ok_count;
    public int $ng_count;
    public function __construct(int $production_line_id, int $ok_count, int $ng_count)
    {
        $this->production_line_id = $production_line_id;
        $this->ok_count = $ok_count;
        $this->ng_count = $ng_count;
    }
}
