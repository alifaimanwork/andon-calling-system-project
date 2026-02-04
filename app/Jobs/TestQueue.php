<?php

namespace App\Jobs;


use App\Models\Downtime;
use App\Models\DowntimeEvent;
use App\Models\ETTOP10Log;
use App\Models\ETTOP20Log;
use App\Models\Plant;
use App\Models\Production;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class TestQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $message;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($message)
    {
        $this->message = $message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('TestQueue[' . $this->message . '] JOB STARTED');
        $micro = microtime(true);
        //Trigger shiftEnded for auto close
        Log::info('TestQueue[' . $this->message . '] JOB ENDED ' . (microtime(true) - $micro));
    }
}
