<?php

declare(strict_types=1);

namespace App\Extras\Utils;

class ModelUtils
{
    public static function copyFields($src, &$dst, $columns = null, $ignoreNullValue = true)
    {
        if (is_null($columns))
            $columns = $src->getConnection()->getSchemaBuilder()->getColumnListing($src->getTable());
        
            
        foreach ($columns as $column) {


            if (isset($src->$column) || !$ignoreNullValue)
                $dst->$column = $src->$column;


        }
    }
}
