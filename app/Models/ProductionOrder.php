<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Database Columns
 * 
 * @property int $id Primary Key: unsigned long integer
 * 
 * @property int $plant_id Foreign Key (plant): unsigned long integer
 * @property int $part_id Foreign Key (part): unsigned long integer
 * @property int $work_center_id Foreign Key (workCenter): unsigned long integer
 * 
 * @property string $order_no string
 * @property int $plan_quantity int
 * @property string $unit_of_measurement string
 * @property int $status tinyinteger 
 * 
 * @property int $actual_output int
 * @property int $pending_count int
 * @property int $ok_count int
 * @property int $ng_count int
 * 
 * @property string $pps_seq string
 * @property string $pps_plant string
 * @property string $pps_factory string
 * @property string $pps_line string
 * @property string $pps_part_no string
 * @property string $pps_part_name string
 * @property string $pps_shift string
 * 
 * @property string $pps_status string
 * @property string $pps_filename string
 * @property string $pps_filehash string
 * 
 * @property \Carbon\Carbon $plan_start timestamp
 * @property \Carbon\Carbon $plan_finish timestamp
 * @property \Carbon\Carbon $created_at timestamp
 * @property \Carbon\Carbon $updated_at timestamp
 */


class ProductionOrder extends Model
{
    /** Production order not started yet*/
    const STATUS_PPS = 0;

    /** Production order already started but not complete */
    const STATUS_INCOMPLETE = 1;

    /** Production order already started & currently running on work center line */
    const STATUS_ONGOING = 2;

    /** Production order completed & closed */
    const STATUS_COMPLETED = 4;

    const TABLE_NAME = 'production_orders';
    protected $table = self::TABLE_NAME;


    protected $appends = ['line_no'];
    protected $guarded = [];

    protected $casts = [
        'plan_start' => 'datetime',
        'plan_finish' => 'datetime',
    ];

    public function getLineNoAttribute()
    {
        /** @var \App\Models\Part $part */
        $part = $this->part;
        if (!$part)
            return null;

        return $part->line_no;
    }

    /** Create Production Line Session */
    public function createProductionLine(Production $production = null)
    {
        $productionLine = new ProductionLine([
            'production_order_id' => $this->id,
            'production_id' => $production->id ?? null,
            'plan_quantity' => $this->getBalancePlanQuantity()
        ]);
        $productionLine->setConnection($this->getConnectionName());
        $productionLine->snapshotPartData();
        if ($productionLine->part_data)
            $productionLine->line_no = $productionLine->part_data['line_no'];
        else
            $productionLine->line_no = 0;


        return $productionLine;
    }
    public function getBalancePlanQuantity()
    {
        //Plan quantity - total ok count from all production line
        /** @var \App\Models\Plant $plant */
        $plant = $this->plant;

        if (!$plant)
            return $this->plan_quantity;

        $countAvg = DB::connection($plant->onPlantDb()->getPlantConnection())
            ->table('production_lines')
            ->where('production_order_id', $this->id)
            ->select([DB::raw('SUM(`production_lines`.`ok_count`) as ok_count')])
            ->first();

        // $productionLines = $this->productionLines()->get();

        // $totalOkCount = 0;
        // /** @var \App\Models\ProductionLine $productionLine */
        // foreach ($productionLines as $productionLine) {
        //     $totalOkCount += $productionLine->ok_count;
        // }

        return $this->plan_quantity - $countAvg->ok_count;
    }

    public static function getFromPath(Plant $plant, string $path)
    {
        if (!$path || strlen($path) <= 0) {
            return [];
        }

        //append leading / at the end
        if ($path[-1] != DIRECTORY_SEPARATOR)
            $path .= DIRECTORY_SEPARATOR;

        $files_txt = glob($path . "*.txt"); //get all files with .txt extension
        $files_csv = glob($path . "*.csv"); //get all files with .csv extension

        $files = array_merge($files_txt, $files_csv);
        $resultPps = [];

        foreach ($files as $file) {

            //try open file
            if (!($fhandle = fopen($file, 'r')))
                continue; //failed


            $fileHash = hash_file('sha256', $file);
            if (!$fileHash)
                continue;


            while ($row = fgetcsv($fhandle, 0, ';')) {

                if (count($row) < 14)
                    continue;

                $values = [];
                foreach ($row as $value) {
                    $values[] = trim($value);
                }

                //Validate sequence no
                if (!is_numeric($values[0]) || floatval($values[0]) != intval($values[0]))
                    continue;

                $timeZone = $plant->getLocalDateTimeZone();

                //validate date time (plan_start_datetime)
                if (!($plan_start =  createDateTime('Y-m-dH:i', $values[8] . $values[9], $timeZone)))
                    continue;

                //validate date time (plan_start_datetime)
                if (!($plan_finish =  createDateTime('Y-m-dH:i', $values[10] . $values[11], $timeZone)))
                    continue;

                $plan_start->setTimezone(new \DateTimeZone('UTC')); //store database in UTC
                $plan_finish->setTimezone(new \DateTimeZone('UTC'));


                $newPps = new ProductionOrder();
                $newPps->pps_filename = basename($file);
                $newPps->pps_filehash = $fileHash;
                $newPps->pps_seq = intval($values[0]);
                $newPps->pps_status = $values[1];
                $newPps->pps_plant = $values[2];
                $newPps->pps_factory = $values[3];
                $newPps->pps_line = $values[4];
                $newPps->order_no = $values[5];
                $newPps->pps_part_no = $values[6];
                $newPps->pps_part_name = $values[7];
                $newPps->plan_start = $plan_start->format('Y-m-d H:i');
                $newPps->plan_finish = $plan_finish->format('Y-m-d H:i');
                $newPps->pps_shift = $values[12];
                $newPps->plan_quantity = $values[13];
                $newPps->unit_of_measurement = $values[14];


                /** @var \App\Models\WorkCenter $workCenter */
                $workCenter = $plant->onPlantDb()->workCenters()->where(WorkCenter::TABLE_NAME . '.name', '=', $newPps->pps_line)->first();
                if (!$workCenter)
                    continue;


                $newPps->setConnection($workCenter->getConnectionName());
                $newPps->work_center_id = $workCenter->id;
                $newPps->plant_id = $plant->id;

                //$part = Part::on($workCenter->getConnectionName())->where('part_no', '=', $newPps->pps_part_no)->first();
                $newPps->part_id = null; //resolve by calling resolvePpsPartNotFound in work center $part->id ?? null;

                $resultPps[] = $newPps;
            }

            fclose($fhandle);
        }

        return $resultPps;
    }
    public function getRecordFromDatabase()
    {
        //check already cached in database
        return ProductionOrder::on($this->connection)
            ->where('pps_filehash', '=', $this->pps_filehash)
            ->where('pps_seq', '=', $this->pps_seq)
            ->where('order_no', '=', $this->order_no)
            ->where('pps_part_no', '=', $this->pps_part_no)
            ->where('pps_part_name', '=', $this->pps_part_name)
            ->where('pps_line', '=', $this->pps_line)
            ->where('plan_start', '=', $this->plan_start)
            ->where('plan_finish', '=', $this->plan_finish)
            ->where('pps_shift', '=', $this->pps_shift)
            ->first();
    }


    public function updateActualOutput()
    {
        $result = DB::connection($this->getConnectionName())
            ->table(ProductionLine::TABLE_NAME)
            ->select([DB::raw("SUM(`" . ProductionLine::TABLE_NAME . "`.`actual_output`) AS total")])->where('production_order_id', '=', $this->id)->first();

        $this->actual_output = ($result->total ?? 0);

        return $this;
    }
    public function updateOkCount()
    {
        $this->ok_count = $this->actual_output - $this->reject_count;
        return $this;
    }
    //relationships

    //belongto plant_id
    public function plant()
    {
        return $this->belongsTo(Plant::class, 'plant_id', 'id');
    }

    //belongto part_id
    public function part()
    {
        return $this->belongsTo(Part::class, 'part_id', 'id');
    }

    //belongto work_center
    public function workCenter()
    {
        return $this->belongsTo(WorkCenter::class, 'work_center_id', 'id');
    }


    //hasmany production_lines
    public function productionLines()
    {
        return $this->hasMany(ProductionLine::class, 'production_order_id', 'id');
    }
    //hasmanythrough
    public function productions()
    {
        return $this->hasManyThrough(Production::class, ProductionLine::class, 'production_order_id', 'id', 'id', 'production_id');
    }
}
