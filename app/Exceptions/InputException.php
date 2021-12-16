<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class InputException extends GeneralException
{
    protected $code = 422;
    protected $type = 'InputException';

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, isset($code)?$code:$this->code, $previous);
    }

}
