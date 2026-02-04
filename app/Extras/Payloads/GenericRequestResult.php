<?php

declare(strict_types=1);

namespace App\Extras\Payloads;

use App\Extras\Support\ResultData;

class GenericRequestResult extends DataPayload
{
    const RESULT_OK = 0;
    const RESULT_INVALID_STATUS = -1;
    const RESULT_INVALID_PARAMETERS = -2;
    const RESULT_RESTRICTED = -3;

    public function __construct($result, $message, $data = null)
    {
        $this->result = $result;
        $this->message = $message;
        if ($data instanceof (ResultData::class)) {
            $this->data = $data->data;
        } else {
            $this->data = $data;
        }
    }
    public $result;
    public $message;
    public $data;
}
