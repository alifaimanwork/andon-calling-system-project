<?php

namespace App\Models;

use App\Extras\Casts\AsNullableArrayObject;
use App\Extras\SapExport\ExportableLog;
use App\Extras\SapExport\ExportableLogModel;
use App\Extras\SapExport\GRNG;
use App\Extras\SapExport\GRNGExport;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Database Columns
 * 
 * @property int $id Primary Key: unsigned integer
 * 
 * @property int $production_line_id  Foreign Key(ProductionLine): unsigned integer
 * 
 * @property string $file_name file name
 * @property string $export_path output path
 * @property array $data report data
 * 
 * @property string $created_at timestamp
 */
class ETTOP20Log extends ExportableLogModel
{
    const TABLE_NAME = 'ett_op20_logs';
    protected $table = self::TABLE_NAME;

    const UPDATED_AT = null;

    protected $casts = ['data' => AsNullableArrayObject::class];


    // ExportableLog Implement //
    public function getExportPath(): string
    {
        return $this->export_path;
    }
    public function getFileName(): string
    {
        return $this->file_name;
    }
    public function generateContent(): string
    {
        //generate content for ETTOP10

        $production_order = $this->data['production_order'] ?? '';
        $yield = $this->data['yield'] ?? '';
        $scrap = $this->data['scrap'] ?? '';
        
        $date_start = $this->data['date_start'] ?? '';
        $date_finish = $this->data['date_finish'] ?? '';
        $time_start = $this->data['time_start'] ?? '';
        $time_finish = $this->data['time_finish'] ?? '';

        //combine all data into one string separated by ';'
        $content = $production_order . ";" .
            $yield . ";" .
            $scrap . ";" .
            $date_start . ";" .
            $date_finish . ";" .
            $time_start . ";" .
            $time_finish;

        return $content;
    }
    // ------ //

    public function updateData()
    {
        /** @var \App\Models\ProductionLine $productionLine */
        $productionLine = $this->productionLine;


        $partData = $productionLine->part_data;

        /** @var \App\Models\ProductionOrder $productionOrder */
        $productionOrder = $productionLine->productionOrder;

        /** @var \App\Models\Production $production */
        $production = $productionLine->production;

        /** @var \App\Models\WorkCenter $workCenter */
        $workCenter = $production->workCenter;

        // /** @var \App\Models\ShiftType $shiftType */
        // $shiftType = $production->shiftType;

        // /** @var \App\Models\User $user */
        // $user = $this->user;

        // $employee = $production->die_change_info['man_power'];

        /** @var \App\Models\Plant $plant */
        $plant = $workCenter->plant;
        if(!$plant)
            return [];

        //ETT20 yield: total OK
        $yield = $productionLine->ok_count;

        //ETT20 scrap: reject setting only
        $scrap = $productionLine->getTotalRejectCount([RejectGroup::REJECT_SETTING]);

        $dtStart = $plant->getLocalDateTime($production->started_at);
        $dtEnd = $plant->getLocalDateTime($production->stopped_at);

        $this->data = [
            'production_order' => $productionOrder->order_no, //Production Order Number
            'yield' => $yield, //Total OK Part
            'scrap' => $scrap, //Total Reject setting
            'date_start' => $dtStart->format('dmY'), //Actual production start date
            'date_finish' => $dtEnd->format('dmY'), //Actual production finish date
            'time_start' => $dtStart->format('Hi'), //Actual production start time (after terminal press start production)
            'time_finish' => $dtEnd->format('Hi'), //Actual production stop time (after terminal press stop production)
        ];


        //filename
        $workCenterName = $workCenter->name;
        $lineNumber = $productionLine->line_no;

        //count work_center characters. if less than 8, add leading 0
        $work_center_length = strlen($workCenterName);

        if ($work_center_length < 8) {
            $workCenterName = str_pad($workCenterName, 8, "0", STR_PAD_LEFT);
        }

        $dtNow = $workCenter->plant->getLocalDateTime();

        $this->file_name = "ETT20" . $dtNow->format('dmYHi') . $workCenterName . $lineNumber;

        //export path
        $this->export_path = $workCenter->ett20_destination;

        return $this;
    }


    // ---- //

    //relationships

    //belongto production line
    public function productionLine()
    {
        return $this->belongsTo(productionLine::class, 'production_line_id', 'id');
    }

    //belongto production line
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
