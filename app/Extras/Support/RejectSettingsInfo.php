<?php

declare(strict_types=1);

namespace App\Extras\Support;

class RejectSettingsInfo extends JsonDataObject
{
    public $maintenance;
    public $quality;
    public $total_reject;
}
