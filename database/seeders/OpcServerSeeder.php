<?php

namespace Database\Seeders;

use App\Models\OpcActiveTag;
use App\Models\OpcServer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OpcServerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $opcServer = new OpcServer([
            'name' => 'default',
            'hostname' => '127.0.0.1',
            'port' => 52240
        ]);
        $opcServer->saveQuietly();

        // for populating tag, use command opc:seed_tag_from_server 1

        // $testTags = ['ns=2;s=IAV T6.Prod Count 1 Data', 'ns=2;s=IAV T6.Prod Count 2 Data','ns=2;s=IAV T6.$Status'];
        // foreach ($testTags as $tag) {
        //     (new OpcActiveTag([
        //         'opc_server_id' => $opcServer->id,
        //         'tag' => $tag,
        //         'data_type' => 'USHORT'
        //     ]))->save();
        // }
    }
}
