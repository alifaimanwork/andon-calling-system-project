<?php

declare(strict_types=1);

namespace App\Extras\Utils;

use PhpOffice\PhpSpreadsheet\Cell\DataType;

class ExcelTemplate
{
    public $template;
    public $data;
    public function __construct(string $srcPath, array $data)
    {
        $this->template = $srcPath;
        $this->data = $data;
    }

    public function render()
    {
        $path = storage_path('app' . DIRECTORY_SEPARATOR . 'dpr_templates' . DIRECTORY_SEPARATOR . 'dpr_smi.xlsx');

        //load spreadsheet
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();

        $spreadsheet = $reader->load($path);


        $sheetCount = $spreadsheet->getSheetCount();
        for ($i = 0; $i < $sheetCount; $i++) {
            $sheet = $spreadsheet->getSheet($i);

            foreach ($sheet->getRowIterator() as $row) {
                foreach ($row->getCellIterator() as $cell) {
                    $val = $cell->getValue();
                    if (!$val)
                        continue;
                    $match = [];

                    preg_match_all('/{(.*)}/U', (string)$val, $match);
                    if (count($match[0]) <= 0) {
                        continue;
                    }


                    for ($n = 0; $n < count($match[0]); $n++) {
                        $key = $match[0][$n];
                        $replaceVal = $this->data[$match[1][$n]] ?? '';
                        $val = str_replace($key, strval($replaceVal), $val);
                    }

                    $cell->setValueExplicit(strval($val), DataType::TYPE_STRING);
                }
            }
        }

        return $spreadsheet;
        //$writer->save('test.xlsx');
    }
}
