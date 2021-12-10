<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class NotFoundException extends RinghelException
{
    protected $code = 404;
    protected $type = 'NotFoundException';

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, isset($code)?$code:$this->code, $previous);
    }

}