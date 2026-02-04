<?php

namespace App\Models;

use App\Extras\Support\ModelDestroyable;
use App\Extras\Utils\ModelUtils;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

/**
 * @method string getTimezoneText()
 * @method \DateTime getLocalDateTime()
 * @method string getRegionFlag()
 * @method string | null getOverviewLayout()
 * @method self onPlantDb()
 * @method self onMainDb()
 * @method string getPlantConnection()
 * @method bool loadAppDatabase()
 * @method self migrateAppDatabase()
 * @method self syncAllData()
 * @method self syncDashboardLayout()
 * @method self syncPlantData()
 * @method self syncCompanyData()
 * @method self syncRegionData()
 * @method self syncUsers()
 * @method self syncOpcServer()
 * @method \Illuminate\Database\Eloquent\Relations\BelongsTo region()
 * @method \Illuminate\Database\Eloquent\Relations\BelongsTo company()
 * @method \Illuminate\Database\Eloquent\Relations\HasMany parts()
 * @method \Illuminate\Database\Eloquent\Relations\HasMany rejectTypes()
 * @method \Illuminate\Database\Eloquent\Relations\HasMany factories()
 * @method \Illuminate\Database\Eloquent\Relations\hasManyThrough workCenters()
 * @method \Illuminate\Database\Eloquent\Relations\hasMany networkNodes()
 * @method \Illuminate\Database\Eloquent\Relations\hasMany productionOrders()
 * @method \Illuminate\Database\Eloquent\Relations\hasMany shift()
 * @method \Illuminate\Database\Eloquent\Relations\hasMany downtimes()
 * @method \Illuminate\Database\Eloquent\Relations\hasMany reasons()
 * @method \Illuminate\Database\Eloquent\Relations\hasMany opcServers()
 * @method \Illuminate\Database\Eloquent\Relations\hasMany opcActiveTags()
 */


/**
 * Database Columns
 * 
 * @property int $id Primary Key: unsigned long integer
 * 
 * @property string $uid string
 * @property string $sap_id string
 * 
 * @property string $name string
 * @property string $time_zone string
 * @property string $total_employee string
 * @property string $total_production_line string
 * @property string $overview_layout_data mediumText
 * @property string $database_configurations text
 * 
 * @property string $created_at timestamp
 * @property string $updated_at timestamp
 * 
 * */


class Plant extends Model implements ModelDestroyable
{
    const TABLE_NAME = 'plants';
    protected $table = self::TABLE_NAME;

    protected $hidden = [
        'database_configurations',
        'overview_layout_data'
    ];

    public static $activePlant = null;
    public function setActivePlant()
    {
        Plant::$activePlant = $this;
        return $this;
    }
    public static function getActivePlant(): Plant|null
    {
        return Plant::$activePlant;
    }


    protected $appDbLoaded = false;
    protected $guarded = [];


    //Utils
    public function getTimezoneText()
    {
        return 'GMT ' . $this->getLocalDateTime()->format('P');
    }
    public function getLocalDateTime(string $datetime = null): \DateTime|null
    {

        if (!$datetime)
            return new \DateTime('now', $this->getLocalDateTimeZone());
        else
            return \DateTime::createFromFormat('Y-m-d H:i:s', $datetime)->setTimezone($this->getLocalDateTimeZone());
    }
    public function getRegionFlag()
    {
        $flagFilename = $this->region->flag ?? null;
        if (!$flagFilename || strlen($flagFilename) <= 0)
            return url('images/flags/my.png'); //url xde flag

        return url('images/flags/' . $flagFilename);
    }

    public function getLocalDateTimeZone(): \DateTimeZone
    {
        return new \DateTimeZone($this->time_zone);
    }
    public function getOverviewLayout()
    {
        //TODO: use file base instead
        return $this->overview_layout_data;
    }

    public function regenerateShifts()
    {
        if (!$this->appDbLoaded)
            $this->loadAppDatabase();

        $currentConnection = $this->connection;

        $shifts = $this->onPlantDb()->shift()->get();

        $shiftData = [];

        $shiftTypes = ShiftType::on($this->getPlantConnection())->get();
        foreach ($shiftTypes as $shiftType) {
            $shiftData[$shiftType->id] = [];
        }

        /** @var \App\Models\Shift $shift */
        foreach ($shifts as $shift) {
            if (!isset($shiftData[$shift->shift_type_id])) //invalid shift type
            {
                $shift->delete();
                continue;
            }

            if ($shift->day_of_week < 1 || $shift->day_of_week > 7) //invalid day of week
            {
                $shift->delete();
                continue;
            }

            if (isset($shiftData[$shift->shift_type_id][$shift->day_of_week])) //duplicate
            {
                $shift->delete();
                continue;
            }
            $shiftData[$shift->shift_type_id][$shift->day_of_week] = $shift;
        }

        //default shift
        $defaultShiftDefs = [
            ['start_time' => '07:30', 'normal_duration' => 33600, 'duration' => 43200],
            ['start_time' => '19:30', 'normal_duration' => 34200, 'duration' => 43200]
        ];

        $shiftDefIdx = 0;
        foreach ($shiftTypes as $shiftType) {
            $defaultShiftDef = $defaultShiftDefs[$shiftDefIdx++] ?? null;

            for ($n = 1; $n <= 7; $n++) {
                if (!isset($shiftData[$shiftType->id][$n])) {
                    //no shift in current block, create new
                    $newShift = new Shift();

                    $newShift->plant_id = $this->id;
                    $newShift->shift_type_id = $shiftType->id;

                    $newShift->day_of_week = $n;
                    $newShift->start_time = $defaultShiftDef['start_time'] ?? $defaultShiftDefs[0]['start_time'];
                    $newShift->duration = $defaultShiftDef['duration'] ?? $defaultShiftDefs[0]['duration'];
                    $newShift->normal_duration = $defaultShiftDef['normal_duration'] ?? $defaultShiftDefs[0]['normal_duration'];
                    $newShift->enabled = $defaultShiftDef ? 1 : 0;
                    $newShift->setConnection($this->getPlantConnection())
                        ->save();
                }
            }
        }
    }

    //Database Utilities
    public function onPlantDb()
    {
        if (!$this->appDbLoaded)
            $this->loadAppDatabase();

        $this->setConnection($this->getPlantConnection());
        return $this;
    }
    public function onMainDb()
    {
        $this->setConnection(null);
        return $this;
    }
    public function getPlantConnection()
    {
        return 'plant_db_' . $this->id;
    }
    public function loadAppDatabase()
    {
        $dbConfig = Config::get('database')['connections']['plant_base_default'];

        $plantDbConfig = json_decode($this->database_configurations);

        //validate
        if (!$plantDbConfig || !isset(
            $plantDbConfig->host,
            $plantDbConfig->port,
            $plantDbConfig->database,
            $plantDbConfig->username,
            $plantDbConfig->password
        ))
            return false;

        $dbConfig['host'] = $plantDbConfig->host;
        $dbConfig['port'] = $plantDbConfig->port;
        $dbConfig['database'] = $plantDbConfig->database;
        $dbConfig['username'] = $plantDbConfig->username;
        $dbConfig['password'] = $plantDbConfig->password;

        $dbConnectionKey = $this->getPlantConnection();
        $currentConfig = Config::get('database');
        $currentConfig['connections'][$dbConnectionKey] = $dbConfig;
        DB::purge($dbConnectionKey);
        Config::set('database', $currentConfig);
        $this->appDbLoaded = true;
        return true;
    }
    public function migrateAppDatabase($outputBuffer = null)
    {
        Artisan::call('migrate --force --database="' . $this->getPlantConnection() . '" --path=/database/migrations/plant --path=/database/migrations', [], $outputBuffer);
        /*
        try {
            Artisan::call('migrate --database="' . $this->getPlantConnection() . '" --path=/database/migrations/plant --path=/database/migrations');
        } catch (Exception $e) {
            $baseError = $e;
            $prevError = $baseError;
            while ($baseError != null) {
                $prevError = $baseError;
                $baseError = $baseError->getPrevious();
            }
            dd($prevError->getMessage());
        }

        dd(Artisan::output());
        */
        return $this;
    }
    public function syncAllData()
    {
        return $this->syncPlantData()
            ->syncRegionData()
            ->syncCompanyData()
            ->syncUsers()
            ->syncOpcServer()
            ->syncDashboardLayout()
            ->syncShiftType()
            ->syncRejectGroup()
            ->syncDowntimeType()
            ->syncOpcTagsType()
            ->regenerateShifts();
    }
    public function syncDashboardLayout()
    {
        $dashboardLayouts = DashboardLayout::get();
        foreach ($dashboardLayouts as $src) {
            $dst = DashboardLayout::on($this->getPlantConnection())->find($src->id);

            if (!$dst) {
                $dst = new DashboardLayout();
                $dst->connection = $this->getPlantConnection();
            }
            ModelUtils::copyFields($src, $dst);

            $dst->save();
        }
        return $this;
    }
    public function syncPlantData()
    {
        $src = $this;
        $dst = Plant::on($this->getPlantConnection())->find($src->id);

        if (!$dst) {
            $dst = new Plant();
            $dst->connection = $this->getPlantConnection();
        }
        ModelUtils::copyFields($src, $dst);

        $dst->save();

        return $this;
    }
    public function syncCompanyData()
    {
        $this->setConnection(null);
        $src = $this->company;
        $dst = Company::on($this->getPlantConnection())->find($src->id);

        if (!$dst) {
            $dst = new Company();
            $dst->connection = $this->getPlantConnection();
        }
        ModelUtils::copyFields($src, $dst);

        $dst->save();
        return $this;
    }
    public function syncRegionData()
    {
        $this->setConnection(null);
        $src = $this->region;
        $dst = Region::on($this->getPlantConnection())->find($src->id);

        if (!$dst) {
            $dst = new Region();
            $dst->connection = $this->getPlantConnection();
        }
        ModelUtils::copyFields($src, $dst);

        $dst->save();
        return $this;
    }
    public function syncUsers()
    {
        $users = User::get();
        foreach ($users as $src) {
            $dst = User::on($this->getPlantConnection())->find($src->id);
            if (!$dst) {
                $dst = new User();
                $dst->connection = $this->getPlantConnection();
            }
            ModelUtils::copyFields($src, $dst);
            $dst->save();
        }
        return $this;
    }
    public function syncOpcServer()
    {
        $opcServers = $this->opcServers()->get();
        foreach ($opcServers as $src) {
            $dst = OpcServer::on($this->getPlantConnection())->find($src->id);
            if (!$dst) {
                $dst = new OpcServer();
                $dst->connection = $this->getPlantConnection();
            }
            ModelUtils::copyFields($src, $dst);
            $dst->save();
        }
        return $this;
    }

    //Sync Lookup Tables
    public function syncShiftType()
    {
        $shiftTypes = ShiftType::get();

        foreach ($shiftTypes as $shiftType) {
            $target = ShiftType::on($this->getPlantConnection())->find($shiftType->id);
            if (!$target) {
                $target = new ShiftType();
                $target->setConnection($this->getPlantConnection());
            }
            ModelUtils::copyFields($shiftType, $target);
            $target->save();
        }
        return $this;
    }
    public function syncRejectGroup()
    {
        //TODO
        $rejectGroups = RejectGroup::get();

        foreach ($rejectGroups as $rejectGroup) {
            $target = RejectGroup::on($this->getPlantConnection())->find($rejectGroup->id);
            if (!$target) {
                $target = new RejectGroup();
                $target->setConnection($this->getPlantConnection());
            }
            ModelUtils::copyFields($rejectGroup, $target);
            $target->save();
        }
        return $this;
    }
    public function syncDowntimeType()
    {
        $downtimeTypes = DowntimeType::get();

        foreach ($downtimeTypes as $downtimeType) {
            $target = DowntimeType::on($this->getPlantConnection())->find($downtimeType->id);
            if (!$target) {
                $target = new DowntimeType();
                $target->setConnection($this->getPlantConnection());
            }
            ModelUtils::copyFields($downtimeType, $target);
            $target->save();
        }
        return $this;
    }
    public function syncOpcTagsType()
    {
        $opcTagTypes = OpcTagType::get();

        foreach ($opcTagTypes as $opcTagType) {
            $target = OpcTagType::on($this->getPlantConnection())->find($opcTagType->id);
            if (!$target) {
                $target = new OpcTagType();
                $target->setConnection($this->getPlantConnection());
            }
            ModelUtils::copyFields($opcTagType, $target);
            $target->save();
        }
        return $this;
    }

    public function isDestroyable(string &$reason = null): bool
    {
        //TODO, only return true when no other resource references to this
        return false;
    }

    //relationships

    //belongto region_id 
    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id', 'id');
    }

    //belongto company_id
    public function company()
    {

        //class
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    //hasmany parts
    public function parts()
    {
        return $this->hasMany(Part::class, 'plant_id', 'id');
    }

    //hasmany reject_types
    public function rejectTypes()
    {
        return $this->hasMany(RejectType::class, 'plant_id', 'id');
    }

    //hasmany factories
    public function factories()
    {
        return $this->hasMany(Factory::class, 'plant_id', 'id');
    }

    //hasmany work_centers
    public function workCenters()
    {
        //return $this->hasManyThrough(WorkCenter::class, Factory::class, 'plant_id', 'factory_id', 'id', 'id');
        return $this->hasMany(WorkCenter::class, 'plant_id', 'id');
    }

    //hasmany network_nodes
    public function networkNodes()
    {
        return $this->hasMany(NetworkNode::class, 'plant_id', 'id');
    }

    //hasmany production_orders
    public function productionOrders()
    {
        return $this->hasMany(ProductionOrder::class, 'plant_id', 'id');
    }

    //hasmany shift
    public function shift()
    {
        return $this->hasMany(Shift::class, 'plant_id', 'id');
    }

    //hasmany downtimes
    public function downtimes()
    {
        return $this->hasMany(Downtime::class, 'plant_id', 'id');
    }

    public function opcTags()
    {
        return $this->hasMany(OpcTag::class, 'plant_id', 'id');
    }

    public function opcServers()
    {
        return $this->belongsToMany(OpcServer::class, 'opc_server_plant', 'opc_server_id', 'plant_id');
    }
    public function opcActiveTags()
    {
        return $this->belongsToMany(OpcActiveTag::class, 'opc_active_tag_plant', 'opc_active_tag_id', 'plant_id');
    }

    public function breakSchedules()
    {
        return $this->hasMany(BreakSchedule::class, 'plant_id', 'id');
    }

    //user belong to many 
    public function users()
    {
        return $this->belongsToMany(User::class, 'plant_user', 'plant_id', 'user_id')->withPivot(['web_permission', 'terminal_permission', 'role']);
    }

    public function monitorClients()
    {
        return $this->hasMany(MonitorClient::class, 'plant_id', 'id');
    }
}
