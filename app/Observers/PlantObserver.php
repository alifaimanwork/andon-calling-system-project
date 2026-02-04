<?php

namespace App\Observers;

use App\Models\OpcServer;
use App\Models\Plant;

class PlantObserver
{
    //TODO: Test multi DB side effects for all editing operation

    /**
     * Handle the Plant "created" event.
     *
     * @param  \App\Models\Plant  $plant
     * @return void
     */
    public function created(Plant $plant)
    {
        //
        if (is_null($plant->connection)) {
            $plant->loadAppDatabase();
            $plant->migrateAppDatabase();
            $plant->syncAllData();
        }
    }

    /**
     * Handle the Plant "updated" event.
     *
     * @param  \App\Models\Plant  $plant
     * @return void
     */
    public function updated(Plant $plant)
    {
        //
        if (is_null($plant->connection))
        {
            $plant->loadAppDatabase();
            $plant->syncPlantData();
        }
    }

    /**
     * Handle the Plant "deleted" event.
     *
     * @param  \App\Models\Plant  $plant
     * @return void
     */
    public function deleted(Plant $plant)
    {
        //
        //if (is_null($plant->connection))
        //$plant->syncToAllPlants();
    }

    /**
     * Handle the Plant "restored" event.
     *
     * @param  \App\Models\Plant  $plant
     * @return void
     */
    public function restored(Plant $plant)
    {
        //TODO:resync data from main db
        if (is_null($plant->connection))
        {
            $plant->loadAppDatabase();
            $plant->syncAllData();
        }
    }

    /**
     * Handle the Plant "force deleted" event.
     *
     * @param  \App\Models\Plant  $plant
     * @return void
     */
    public function forceDeleted(Plant $plant)
    {
        //
        //if (is_null($plant->connection))
        //$plant->syncToAllPlants();
    }
}
