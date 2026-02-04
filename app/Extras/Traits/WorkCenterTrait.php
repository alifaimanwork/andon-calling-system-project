<?php

declare(strict_types=1);

namespace App\Extras\Traits;

use App\Models\Plant;
use App\Models\WorkCenter;

trait WorkCenterTrait
{
    public function getPlantWorkCenterLine($plantUid, $workCenterUid = null, $lineNo = 1, $associative = true)
    {
        $data = $this->getPlantWorkCenter($plantUid, $workCenterUid);

        if (is_null($data) ||  (!is_null($data['workCenter']) && ($lineNo > $data['workCenter']->production_line_count || $lineNo <= 0)))
            return null;

        $data['lineNo'] = $lineNo;

        return $associative ? $data : (object)$data;
    }
    public function getPlantWorkCenter($plantUid, $workCenterUid = null, $associative = true)
    {
        /** @var \App\Models\Plant $plant */
        $plant = Plant::where('uid', $plantUid)->first();
        if (!$plant)
            return null;

        $plant->setActivePlant()->loadAppDatabase();

        if (!is_null($workCenterUid)) {
            $workCenter = $plant->onPlantDb()->workCenters()->where(WorkCenter::TABLE_NAME . '.uid', $workCenterUid)->first();
            if (!$workCenter)
                return null;
        } else
            $workCenter = $plant->onPlantDb()->workCenters()->first();





        $data = [
            'plant' => $plant,
            'workCenter' => $workCenter
        ];
        return $associative ? $data : (object)$data;
    }
}
