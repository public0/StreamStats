<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class RinghelException extends Exception implements Throwable
{
    protected $type = 'CoreException';

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, isset($code)?$code:$this->code, $previous);
    }

    public function getType() {
        return $this->type;
    }

}
