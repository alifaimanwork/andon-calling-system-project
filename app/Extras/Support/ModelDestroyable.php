<?php

declare(strict_types=1);

namespace App\Extras\Support;

interface ModelDestroyable
{
    public function isDestroyable(string &$reason = null): bool;
}
