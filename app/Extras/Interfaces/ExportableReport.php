<?php

declare(strict_types=1);

namespace App\Extras\Interfaces;

interface ExportableReport
{
    function export(string $format);
}
