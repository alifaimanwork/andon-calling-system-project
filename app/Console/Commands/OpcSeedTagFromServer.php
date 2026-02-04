<?php

namespace App\Console\Commands;

use App\Models\OpcActiveTag;
use App\Models\OpcServer;
use App\Models\Plant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpcSeedTagFromServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'opc:seed_tag_from_server {server_id} {plant_uid?} {--dryrun}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed OPC Tags from server';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $serverId = $this->argument('server_id');
        $plantUid = $this->argument('plant_uid');
        $dryrun = $this->option('dryrun');

        if ($plantUid) {
            $plant = Plant::where('uid', $plantUid)->first();
            if (!$plant) {
                $this->info("Invalid Plant UID");
                return 0;
            }
        } else {
            $plant = null;
        }

        /** @var \App\Models\OpcServer $opcServer */
        $opcServer = OpcServer::find($serverId);
        if (!$opcServer) {
            $this->info("Invalid Server ID");
            return 0;
        }

        if (!$this->confirm("Seed OPC Tags from " . $opcServer->hostname . ":" . $opcServer->port . ($plant ? ' for ' . $plant->uid : ' WITHOUT ATTACHING TO ANY PLANT') . '?'))
            return 0;

        $response = Http::post(
            'http://127.0.0.1:8000',
            [
                'function' => 'fetch_all_tags',
                'data' => [
                    'host' =>  $opcServer->hostname,
                    'port' =>  $opcServer->port
                ]
            ]
        );

        $data = json_decode($response->body());
        if (!isset($data->result, $data->data)) {
            $this->info("Invalid adapter response");
            return 0;
        }
        if ($data->result !== 'ok') {
            $this->info("Error fetching tag from OPC");
            return 0;
        }

        $excludedName = ['Server', 'SYSTEM'];

        $tags = [];

        foreach ($data->data->childs as $rootElement) {
            $browseName = $rootElement->browse_name ?? null;
            if (!$browseName || in_array($browseName, $excludedName) || !isset($rootElement->childs) || !is_array($rootElement->childs))
                continue;

            $tagGroups = [];

            //$this->warn($browseName);
            foreach ($rootElement->childs as $key => $child) {
                $browseName = $child->browse_name ?? null;
                $tag =  $child->tag ?? null;

                if (!$browseName || !$tag || !is_string($tag) || !is_string($browseName) || $browseName[0] == '$' || $child->class != 'Variable')
                    continue;

                if ($tag != trim($tag))
                    continue;

                //$this->info('[' . $child->browse_name . "] " . $tag);
                $tags[] = $child;
            }
        }


        $opcTags = $opcServer->opcActiveTags()->get();
        $oldTags = [];


        /** @var \App\Models\OpcActiveTag $opcTag */
        foreach ($opcTags as $opcTag) {
            $oldTags[$opcTag->tag] = $opcTag;
        }

        $newTags = [];

        $availableTags = [];
        $this->warn('Updating Tags:');
        $n = 0;
        foreach ($tags as $tag) {
            $availableTags[] = $tag->tag;
            if (isset($oldTags[$tag->tag])) {

                /** @var \App\Models\OpcActiveTag $opcTag */
                $opcTag = $oldTags[$tag->tag];
                $opcTag->plant_id = $plant->id;
                $opcTag->data_type = $tag->data_type;

                if ($opcTag->isDirty())
                    $this->warn('[' . ++$n . '] ' . $tag->tag . ' [UPDATED]');
                else
                    $this->info('[' . ++$n . '] ' . $tag->tag . ' [NO CHANGE]');

                if (!$dryrun)
                    $opcTag->save();

                continue;
            }

            $newTags[] = $tag->tag;

            $this->warn('[' . ++$n . '] ' . $tag->tag . ' [NEW]');
            if ($dryrun)
                continue;


            (new OpcActiveTag([
                'opc_server_id' => $opcServer->id,
                'plant_id' => $plant ? $plant->id : null,
                'tag' => $tag->tag,
                'data_type' => $tag->data_type
            ]))->save();
        }

        $missingTags = [];

        /** @var \App\Models\OpcActiveTag $opcTag */
        foreach ($oldTags as $tag => $opcTag) {
            if (!in_array($tag, $availableTags))
                $missingTags[] = $tag;
        }
        if (count($missingTags) > 0) {
            $n = 0;
            $this->warn("Missing Tags:");
            foreach ($missingTags as $tag) {
                $this->info('[' . ++$n . '] ' . $tag);
            }

            if (!$dryrun && $this->confirm("Remove missing tags?")) {
                /** @var \App\Models\OpcActiveTag $opcTag */
                foreach ($opcTags as $opcTag) {
                    if (in_array($opcTag->tag, $missingTags))
                        $opcTag->delete();
                }
            }
        }

        return 0;
    }
}
