<?php

namespace App\Events;

use App\Models\ActivityLog;

interface ActivityLogEvent
{
    public function generateActivityLog(): ActivityLog;
}