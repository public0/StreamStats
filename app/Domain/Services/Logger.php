<?php

namespace App\Domain\Services;
use App\Exceptions\SQLException;
use App\Models\RequestLog;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Logger {
    public static function log(array $data, Request $req) {
        try {
            $log = new RequestLog;
            $log->Path = $req->path();
            $log->ResponseCode = $data['StatusCode'];
            $log->Message = ($data['StatusCode'] != 200)?$data['Message']:'Ok';
            $log->save();
        } catch (QueryException $e) {
            throw new SQLException($e->getMessage());
        }
    }

}