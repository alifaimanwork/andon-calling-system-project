<?php

namespace App\Observers;

use App\Models\OpcServer;

class OpcServerObserver
{
    /**
     * Handle the OpcServer "created" event.
     *
     * @param  \App\Models\OpcServer  $opcServer
     * @return void
     */
    public function created(OpcServer $opcServer)
    {
        //
        if (is_null($opcServer->connection))
            $opcServer->syncToAllPlants();
    }

    /**
     * Handle the OpcServer "updated" event.
     *
     * @param  \App\Models\OpcServer  $opcServer
     * @return void
     */
    public function updated(OpcServer $opcServer)
    {
        //
        if (is_null($opcServer->connection))
            $opcServer->syncToAllPlants();
    }

    /**
     * Handle the OpcServer "deleted" event.
     *
     * @param  \App\Models\OpcServer  $opcServer
     * @return void
     */
    public function deleted(OpcServer $opcServer)
    {
        //
        if (is_null($opcServer->connection))
            $opcServer->deleteFromAllPlants();
    }

    /**
     * Handle the OpcServer "restored" event.
     *
     * @param  \App\Models\OpcServer  $opcServer
     * @return void
     */
    public function restored(OpcServer $opcServer)
    {
        //
        if (is_null($opcServer->connection))
            $opcServer->syncToAllPlants();
    }

    /**
     * Handle the OpcServer "force deleted" event.
     *
     * @param  \App\Models\OpcServer  $opcServer
     * @return void
     */
    public function forceDeleted(OpcServer $opcServer)
    {
        //
        if (is_null($opcServer->connection))
            $opcServer->deleteFromAllPlants();
    }
}
