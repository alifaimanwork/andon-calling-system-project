<?php

namespace App\Console\Commands;

use App\Models\OpcServer;
use App\Models\Plant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class OpcQueryTagList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'opc:query_tag_list {server_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Query OPC Tag list from server';

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
        $opcServer = OpcServer::find($serverId);
        if (!$opcServer)
            $this->info("Invalid Server ID");

        $this->info("Query tag from " . $opcServer->hostname . ":" . $opcServer->port);

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

            $this->warn($browseName);
            foreach ($rootElement->childs as $key => $child) {
                $browseName = $child->browse_name ?? null;
                $tag =  $child->tag ?? null;
                if(!$browseName || !$tag || !is_string($tag) || !is_string($browseName) || $browseName[0] == '$')
                    continue;

                $this->info('['.$child->browse_name."] ".$tag);
            }

            if(count($tagGroups) > 0)
                return;

            $tags[] = $tagGroups;
        }
        return 0;
    }
}
