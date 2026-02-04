<?php

namespace App\Models;

use App\Extras\Support\ModelDestroyable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use JJG\Ping;

/**
 * Database Columns
 * 
 * @property int $id Primary Key: unsigned integer
 * 
 * @property int $plant_id Foreign Key (Plant): unsigned integer
 * @property int $target_id Foreign Key (WorkCenterId): unsigned integer
 * @property int $client_type Client Type 0: Workcenter
 * @property string $uid Name
 * @property string $uid string
 * @property string $client_info string
 * @property int $state websocket 
 * @property string $last_reported_at timestamp
 * 
 * @property string $created_at timestamp
 * @property string $updated_at timestamp
 */
class MonitorClient extends Model implements ModelDestroyable
{
    const TABLE_NAME = 'monitor_clients';
    protected $table = self::TABLE_NAME;

    const CLIENT_TYPE_TERMINAL = 0;
    const CLIENT_TYPE_DASHBOARD = 1;
    const CLIENT_TYPE_NETWORK_NODE = 2;


    const STATE_UNKNOWN = -1;
    const STATE_OFFLINE = 0;
    const STATE_ONLINE = 1;
    const STATE_WS_ERRROR = 2;
    const STATE_CONFIG_ERROR = 3;
    //utils
    public static function generateNewUid(Plant $plant): string|null
    {
        $retryCount = 0;
        $plant->loadAppDatabase();
        while ($retryCount < 10) {
            $s = self::random_str();
            if (!MonitorClient::on($plant->getPlantConnection())->where('uid', $s)->first())
                return $s;
        }
        return null;
    }
    static function random_str(
        int $length = 64,
        string $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
    ): string {
        if ($length < 1) {
            throw new \RangeException("Length must be a positive integer");
        }
        $pieces = [];
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $pieces[] = $keyspace[random_int(0, $max)];
        }
        return implode('', $pieces);
    }

    public function probe(): self
    {
        if ($this->client_type !== self::CLIENT_TYPE_NETWORK_NODE)
            return $this;

        $clientInfo = json_decode($this->client_info);

        if ($clientInfo && $clientInfo->host) {
            if (filter_var(gethostbyname($clientInfo->host), FILTER_VALIDATE_IP)) {
                $ping = new Ping($clientInfo->host, 255, 5);
                $latency = $ping->ping();
                if (!$latency) {
                    $this->state = self::STATE_OFFLINE;
                    $clientInfo->latency = null;
                } else {
                    $this->state = self::STATE_ONLINE;
                    $clientInfo->latency = $latency;
                }

                $this->client_info = json_encode($clientInfo);
            } else
                $this->state = self::STATE_OFFLINE;
        } else {
            $this->state = self::STATE_OFFLINE;
        }
        $this->last_reported_at = now();
        return $this;
    }


    public function isDestroyable(string &$reason = null): bool
    {
        return true;
    }
    //relationship

    public function plant()
    {
        return $this->belongsTo(Plant::class, 'plant_id', 'id');
    }
}
