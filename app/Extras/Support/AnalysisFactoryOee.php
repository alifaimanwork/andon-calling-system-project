<?php

declare(strict_types=1);

namespace App\Extras\Support;

use App\Extras\Interfaces\ExportableReport;
use App\Models\Factory;
use App\Models\Plant;
use App\Models\Production;
use App\Models\ProductionLine;
use App\Models\ProductionOrder;
use App\Models\ShiftType;
use App\Models\WorkCenter;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Facades\DB;
use JsonSerializable;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Stringable;

class AnalysisFactoryOee extends ResultData implements Arrayable, Jsonable, JsonSerializable, Stringable, ExportableReport
{
    public array $data = [];
    public $factory_uids = null;
    public $plant = null;
    public $date = null;

    public $factories = null;


    public static function create(Plant $plant, array | null $factory_uids, string $date)
    {

        $analysis = new AnalysisFactoryOee();
        $query = DB::connection($plant->onPlantDb()->getPlantConnection())
            ->table(Factory::TABLE_NAME)
            ->join(WorkCenter::TABLE_NAME, Factory::TABLE_NAME . '.id', '=', WorkCenter::TABLE_NAME . '.factory_id', 'left')
            ->join(Production::TABLE_NAME, WorkCenter::TABLE_NAME . '.id', '=', Production::TABLE_NAME . '.work_center_id', 'left')

            ->where(Factory::TABLE_NAME . '.plant_id', '=', $plant->id)
            ->where(Production::TABLE_NAME . '.average_performance', '>', 0) //assume zero performance as no output from all line (exclude from calculation)
            ->where(function ($q) use ($date) {
                $q->where(Production::TABLE_NAME . '.shift_date', '=', $date)
                    ->orWhereNull(Production::TABLE_NAME . '.id');
            })
            ->where(function ($q) {
                $q->where(WorkCenter::TABLE_NAME . '.enabled', '=', 1)
                    ->orWhereNull(WorkCenter::TABLE_NAME . '.id');
            })
            ->where(function ($q) {
                $q->where(Production::TABLE_NAME . '.status', '=', Production::STATUS_STOPPED)
                    ->orWhereNull(Production::TABLE_NAME . '.id');
            })

            ->orderBy(Factory::TABLE_NAME . '.name')
            ->orderBy(WorkCenter::TABLE_NAME . '.name')
            ->groupBy(
                Factory::TABLE_NAME . '.uid',
                Factory::TABLE_NAME . '.name',
                WorkCenter::TABLE_NAME . '.id',
                WorkCenter::TABLE_NAME . '.uid',
                WorkCenter::TABLE_NAME . '.name',
                Production::TABLE_NAME . '.shift_type_id'
            )
            ->select([
                DB::raw(Factory::TABLE_NAME . '.uid as factory_uid'),
                DB::raw(Factory::TABLE_NAME . '.name as factory_name'),

                DB::raw(WorkCenter::TABLE_NAME . '.uid as work_center_uid'),
                DB::raw(WorkCenter::TABLE_NAME . '.name as work_center_name'),

                DB::raw(Production::TABLE_NAME . '.shift_type_id as shift_type_id'),

                DB::raw('AVG(' . Production::TABLE_NAME . '.average_oee) as average_oee'),
                DB::raw('AVG(' . Production::TABLE_NAME . '.average_availability) as average_availability'),
                DB::raw('AVG(' . Production::TABLE_NAME . '.average_performance) as average_performance'),
                DB::raw('AVG(' . Production::TABLE_NAME . '.average_quality) as average_quality')

            ]);

        if ($factory_uids && count($factory_uids) > 0)
            $query->whereIn(Factory::TABLE_NAME . '.uid', $factory_uids);

        $analysis->data = $query->get()->toArray();
        $analysis->date = $date;
        $analysis->plant = $plant;
        $analysis->factory_uids = $factory_uids;
        return $analysis;
    }

    public function toJson($options = 0)
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
    public function toArray()
    {

        $return = [];

        /* foreach factory
            foreach work_center
                foreach shift
                    average_oee
                    average_availability
                    average_performance
                    average_quality
        */

        $class_vars = get_class_vars(get_class($this));

        $return = [];
        foreach ($class_vars as $name => $value) {
            $return[$name] = $this->$name;
        }

        return $return;
    }
    public function __toString()
    {
        return $this->toJson();
    }
    public function collectWorkCenterShiftData($workCenterUid, $data)
    {
        if ($data == null)
            $data = $this->data;

        if (!is_array($data))
            return [];
        $result = [];
        foreach ($data as $item) {
            if ($item->work_center_uid == $workCenterUid)
                $result[$item->shift_type_id] = $item;
        }

        return $result;
    }
    public function expandData(): self
    {
        /** @var \App\Models\Plant $plant */
        $plant = $this->plant;
        if (!$plant)
            return $this;

        if ($this->factory_uids && count($this->factory_uids) > 0)
            $this->factories = $this->plant->factories()->with('workCenters')->orderBy(Factory::TABLE_NAME . '.name')->whereIn(Factory::TABLE_NAME . '.uid', $this->factory_uids)->get();
        else
            $this->factories =  $plant->factories()->with('workCenters')->orderBy(Factory::TABLE_NAME . '.name')->get();

        return $this;
    }
    public function export(string $format = 'xlsx')
    {
        /** @var \App\Models\Plant $plant */
        $plant = $this->plant;
        if (!$plant)
            abort(404);

        $this->expandData();



        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $row = 1;


        $sheet->setCellValue('A' . $row, 'Operational Analysis - Factory OEE');
        $row++;
        $row++;

        $sheet->setCellValue('A' . $row, 'Generated At');
        $sheet->setCellValue('B' . $row, $plant->getLocalDateTime());
        $row++;

        $sheet->setCellValue('A' . $row, 'Plant');
        $sheet->setCellValue('B' . $row, $plant->name);
        $row++;

        $sheet->setCellValue('A' . $row, 'Date');
        $sheet->setCellValue('B' . $row, $this->date);
        $row++;

        $row++;

        $shiftText = [
            ShiftType::DAY_SHIFT => 'Day',
            ShiftType::NIGHT_SHIFT => 'Night'
        ];

        /** @var \App\Models\Factory $factory */
        foreach ($this->factories as $factory) {

            $workCenters = $factory->workCenters;

            $sheet->setCellValue('A' . $row, $factory->name);
            $row++;

            $sheet->mergeCells('B' . $row . ':E' . $row);
            $sheet->setCellValue('B' . $row, 'Day');
            $sheet->mergeCells('F' . $row . ':I' . $row);
            $sheet->setCellValue('F' . $row, 'Night');
            $row++;

            $sheet->setCellValue('A' . $row, 'Work Center');

            $sheet->setCellValue('B' . $row, 'OEE');
            $sheet->setCellValue('C' . $row, 'Availability');
            $sheet->setCellValue('D' . $row, 'Performance');
            $sheet->setCellValue('E' . $row, 'Quality');

            $sheet->setCellValue('F' . $row, 'OEE');
            $sheet->setCellValue('G' . $row, 'Availability');
            $sheet->setCellValue('H' . $row, 'Performance');
            $sheet->setCellValue('I' . $row, 'Quality');
            $row++;

            /** @var \App\Models\WorkCenter $workCenter */
            foreach ($workCenters as $workCenter) {
                $workCenterData = $this->collectWorkCenterShiftData($workCenter->uid, $this->data);
                $sheet->setCellValue('A' . $row, $workCenter->name);

                $sheet->setCellValue('B' . $row, (isset($workCenterData[ShiftType::DAY_SHIFT]->average_oee) && is_numeric($workCenterData[ShiftType::DAY_SHIFT]->average_oee)) ? ($workCenterData[ShiftType::DAY_SHIFT]->average_oee * 100) . '%' : '-');
                $sheet->setCellValue('C' . $row, (isset($workCenterData[ShiftType::DAY_SHIFT]->average_availability) && is_numeric($workCenterData[ShiftType::DAY_SHIFT]->average_availability)) ? ($workCenterData[ShiftType::DAY_SHIFT]->average_availability * 100) . '%' : '-');
                $sheet->setCellValue('D' . $row, (isset($workCenterData[ShiftType::DAY_SHIFT]->average_performance) && is_numeric($workCenterData[ShiftType::DAY_SHIFT]->average_performance)) ? ($workCenterData[ShiftType::DAY_SHIFT]->average_performance * 100) . '%' : '-');
                $sheet->setCellValue('E' . $row, (isset($workCenterData[ShiftType::DAY_SHIFT]->average_quality) && is_numeric($workCenterData[ShiftType::DAY_SHIFT]->average_quality)) ? ($workCenterData[ShiftType::DAY_SHIFT]->average_quality * 100) . '%' : '-');

                $sheet->setCellValue('F' . $row, (isset($workCenterData[ShiftType::NIGHT_SHIFT]->average_oee) && is_numeric($workCenterData[ShiftType::NIGHT_SHIFT]->average_oee)) ? ($workCenterData[ShiftType::NIGHT_SHIFT]->average_oee * 100) . '%' : '-');
                $sheet->setCellValue('G' . $row, (isset($workCenterData[ShiftType::NIGHT_SHIFT]->average_availability) && is_numeric($workCenterData[ShiftType::NIGHT_SHIFT]->average_availability)) ? ($workCenterData[ShiftType::NIGHT_SHIFT]->average_availability * 100) . '%' : '-');
                $sheet->setCellValue('H' . $row, (isset($workCenterData[ShiftType::NIGHT_SHIFT]->average_performance) && is_numeric($workCenterData[ShiftType::NIGHT_SHIFT]->average_performance)) ? ($workCenterData[ShiftType::NIGHT_SHIFT]->average_performance * 100) . '%' : '-');
                $sheet->setCellValue('I' . $row, (isset($workCenterData[ShiftType::NIGHT_SHIFT]->average_quality) && is_numeric($workCenterData[ShiftType::NIGHT_SHIFT]->average_quality)) ? ($workCenterData[ShiftType::NIGHT_SHIFT]->average_quality * 100) . '%' : '-');

                $row++;
            }
            $row++;
        }



        $filename = "analysis_factory_oee_" . $this->date;
        if ($format == 'csv') {
            $writer = new Csv($spreadsheet);
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . urlencode($filename) . '.csv"');
            $writer->save('php://output');
        } else {

            foreach ($sheet->getColumnIterator() as $column) {
                $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
            }

            $writer = new Xlsx($spreadsheet);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . urlencode($filename) . '.xlsx"');
            $writer->save('php://output');
        }
        return;
    }
}
