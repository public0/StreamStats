<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class ValidationException extends GeneralException
{
    protected $code = 401;
    protected $type = 'ValidationException';

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, isset($code)?$code:$this->code, $previous);
    }

}
