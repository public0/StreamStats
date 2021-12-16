<?php


namespace App\Exceptions;

use Throwable;

class BadRequestException extends GeneralException
{
    protected $code = 400;
    protected $type = 'BadRequestException';

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}