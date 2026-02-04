<?php

declare(strict_types=1);

namespace App\Extras\Support\Opc;

use App\Extras\Support\JsonDataObject;

class OpcTagConfig extends JsonDataObject
{
    const TAG_MODE_READ = 1;
    const TAG_MODE_WRITE = 0;

    public ?string $tag = null;
    public ?string $data_type = null;
    public int $mode = self::TAG_MODE_READ;
    public ?int $value = null;
}
