<?php

namespace App\Models;

use App\Extras\Casts\AsNullableArrayObject;
use App\Extras\SapExport\ExportableLogModel;
use App\Extras\SapExport\GRNGExport;


/**
 * Database Columns
 * 
 * @property int $id Primary Key: unsigned integer
 * 
 * @property int $production_line_id  Foreign Key(ProductionLine): unsigned integer
 * @property int $user_id  Foreign Key(User): unsigned integer
 * 
 * 
 * @property int $count_offset count offset
 * @property int $batch_no batch no
 * @property int $count count
 * @property string $file_name file name
 * @property string $export_path output path
 * @property array $data report data
 * 
 * @property string $created_at timestamp
 */
class GROKLog extends ExportableLogModel
{

    const TABLE_NAME = 'gr_ok_logs';
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
        //generate content for GROK
        $production_order = $this->data['production_order'] ?? '';
        $posting_date = $this->data['posting_date'] ?? '';
        $document_header_text = $this->data['document_header_text'] ?? '';
        $material_number = $this->data['material_number'] ?? '';
        $quantity = $this->data['quantity'] ?? '';
        $recipient = $this->data['recipient'] ?? '';
        $batch = $this->data['batch'] ?? '';

        //combine all data into one string separated by ';'
        $content = $production_order . ";" .
            $posting_date . ";" .
            $document_header_text . ";" .
            $material_number . ";" .
            $quantity . ";" .
            $recipient . ";" .
            $batch;

        return $content;
    }
    // ------ //

    public function updateData(ProductionLine $productionLine = null)
    {

        if (!$productionLine) {
            /** @var \App\Models\ProductionLine $productionLine */
            $productionLine = $this->productionLine;
        }

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

        /** @var \App\Models\Plant $plant  */
        $plant = $workCenter->plant;

        $shiftDate = \DateTime::createFromFormat('Y-m-d', $production->shift_date);
        //TODO: fill in required data
        $this->data = [
            'production_order' => $productionOrder->order_no, //Production Order
            'posting_date' => $shiftDate ? $shiftDate->format('dmY') : '', //$plant->getLocalDateTime()->format('dmY'), //Posting Date 
            'document_header_text' => $shiftType->name, //Document Header Text
            'material_number' => $partData['part_no'], //Material Number
            'quantity' => $this->count, //Quantity
            'recipient' => $user->sap_id, //Recipient
            'batch' =>  sprintf('%03d', $this->batch_no)
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

        $this->file_name = "GROK" . $dtNow->format('dmYHi') . $workCenterName . $lineNumber;


        //export path
        $this->export_path = $workCenter->gr_ok_destination;

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
