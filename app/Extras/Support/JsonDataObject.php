<?php

declare(strict_types=1);

namespace App\Extras\Support;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;
use stdClass;
use Stringable;

abstract class JsonDataObject implements Arrayable, Jsonable, JsonSerializable, Stringable
{
    public function populate($data): self | null
    {
        if (is_null($data))
            return null;


        if (!is_object($data)) {
            $obj = json_decode($data, true);
        } else {
            $obj = $data;
        }


        if (!$obj)
            return null;

        $class_vars = get_class_vars(get_class($this));


        foreach ($class_vars as $name => $value) {
            if (!isset($obj[$name]))
                return null;
            $this->$name = $obj[$name];
        }
        return $this;
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
}
