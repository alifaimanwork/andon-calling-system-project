<?php

declare(strict_types=1);

namespace App\Extras\Traits;

use App\Models\WorkCenter;
use Illuminate\Support\Facades\Route;

trait TerminalRoutingTrait
{
    protected $routeMaps = [
        WorkCenter::STATUS_IDLE => 'terminal.production-planning.index',
        WorkCenter::STATUS_DIE_CHANGE => 'terminal.die-change.index',
        WorkCenter::STATUS_FIRST_CONFIRMATION => 'terminal.first-product-confirmation.index',
        WorkCenter::STATUS_RUNNING => 'terminal.progress-status.index',
    ];

    function checkTerminalRoute(WorkCenter $workCenter)
    {

        $correctRoute = $this->routeMaps[$workCenter->status] ?? null;

        if (!$correctRoute)
            abort(404);
        elseif ($correctRoute != Route::getCurrentRoute()->getName())
            return redirect()->route($correctRoute, [$workCenter->plant->uid, $workCenter->uid]);

        return null;
    }
}
