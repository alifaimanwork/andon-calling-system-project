<?php

declare(strict_types=1);

namespace App\Extras\Utils;

class SvgLayoutHelper
{
    public static function removeXmlTag($svgData)
    {
        return preg_replace('/(<\?xml).+?(?=<svg)/s','',$svgData);
    }
}
