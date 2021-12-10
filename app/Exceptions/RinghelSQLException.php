<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Database\QueryException;
use Throwable;

class RinghelSQLException extends QueryException implements Throwable
{
    protected $type = 'CoreException';

    public function __construct($message, array $bindings, Throwable $previous = null)
    {
        parent::__construct($message, $bindings, $previous);
    }

    public function getType() {
        return $this->type;
    }

}
