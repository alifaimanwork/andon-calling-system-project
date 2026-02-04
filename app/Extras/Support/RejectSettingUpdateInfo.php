<?php

declare(strict_types=1);

namespace App\Extras\Support;

class RejectSettingUpdateInfo extends JsonDataObject
{
    public $production_line_id;
    public $add_maintenance;
    public $add_quality;
}
