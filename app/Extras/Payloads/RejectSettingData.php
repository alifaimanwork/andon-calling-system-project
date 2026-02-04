<?php

declare(strict_types=1);

namespace App\Extras\Payloads;

class RejectSettingData extends DataPayload
{
    public int $production_line_id;
    public int $maintenance_count;
    public int $quality_count;
    public function __construct(int $production_line_id, int $maintenance_count, int $quality_count)
    {
        $this->production_line_id = $production_line_id;
        $this->maintenance_count = $maintenance_count;
        $this->quality_count = $quality_count;
    }
}
