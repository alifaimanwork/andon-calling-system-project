<?php

namespace App\Extras\SapExport;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

abstract class ExportableLogModel extends Model
{
    public abstract function getExportPath(): string;
    public abstract function getFileName(): string;
    public abstract function generateContent(): string;
    public function export()
    {
        $extension = ".csv";

        $path = $this->getExportPath() . DIRECTORY_SEPARATOR . $this->getFileName();
        $content = $this->generateContent();

        //check file exist
        $basePath = $path;
        $path = $path . $extension;

        $number = 0;
        while (file_exists($path)) {
            if ($number > 999) //too many try, abort export
            {
                Log::error("Error Export, file exist check. " . $basePath);
                return $this;
            }
            $path = $basePath . sprintf("%03d", ++$number) . $extension;
        }
        try {
            file_put_contents($path, $content);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
        }
        return $this;
    }
}
