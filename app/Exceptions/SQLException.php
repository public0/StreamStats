<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Database\QueryException;
use Throwable;

class SQLException extends RinghelSQLException
{
    protected $code = 500;
    protected $type = 'DatabaseException';

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        switch ($code) {
            case 50000: {
                $this->code = 200;
                break;
            }

            default: {
                $this->code = 502;
            }
        }
        parent::__construct($message, [], $previous);
    }

    public function getStatusCode() {
        return $this->code;
    }

}
