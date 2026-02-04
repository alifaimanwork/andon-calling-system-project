<?php

namespace App\Models;

use App\Extras\Casts\AsNullableArrayObject;
use App\Extras\SapExport\ExportableLogModel;


/**
 * Database Columns
 * 
 * @property int $id Primary Key: unsigned integer
 * 
 * @property int $production_line_id  Foreign Key(ProductionLine): unsigned integer
 * @property int $user_id  Foreign Key(User): unsigned integer
 * 
 * @property string $file_name file name
 * @property string $export_path output path
 * @property array $data report data
 * 
 * @property string $created_at timestamp
 */
class GRQILog extends ExportableLogModel
{

    const TABLE_NAME = 'gr_qi_logs';
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
        //generate content for GRNG
        $production_order = $this->data['production_order'] ?? '';
        $posting_date = $this->data['posting_date'] ?? '';
        $document_header_text = $this->data['document_header_text'] ?? '';
        $material_number = $this->data['material_number'] ?? '';
        $quantity = $this->data['quantity'] ?? '';
        $recipient = $this->data['recipient'] ?? '';

        //combine all data into one string separated by ';'
        $content = $production_order . ";" .
            $posting_date . ";" .
            $document_header_text . ";" .
            $material_number . ";" .
            $quantity . ";" .
            $recipient;

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

        /** @var \App\Models\ShiftType $shiftType */
        $shiftType = $production->shiftType;

        /** @var \App\Models\User $user */
        $user = $this->user;

        $shiftDate = \DateTime::createFromFormat('Y-m-d', $production->shift_date);
        //TODO: fill in required data
        $this->data = [
            'production_order' => $productionOrder->order_no,
            'posting_date' => $shiftDate ? $shiftDate->format('dmY') : '', //$production->stopped_at->format('dmY'),
            'document_header_text' => $shiftType->name,
            'material_number' => $partData['part_no'],
            'quantity' => $productionLine->pending_count,
            'recipient' => $user->sap_id,
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

        $this->file_name = "GRQI" . $dtNow->format('dmYHi') . $workCenterName . $lineNumber;


        //export path
        $this->export_path = $workCenter->gr_qi_destination;

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
