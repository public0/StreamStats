<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;
use App\Domain\Services\Logger;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        $statusCode = method_exists($exception,'getStatusCode')?$exception->getStatusCode():$exception->getCode();

        $message = self::parseResponse($exception->getMessage()?$exception->getMessage():'Internal Server Error!');
        switch ($statusCode) {
            case 42000: {
                if(isset($exception->errorInfo[1])) {
                    switch ($exception->errorInfo[1]) {
                        case 50000: {
                            $statusCode = 422;
                            break;
                        }
                        default: {
                            $statusCode = 500;
                        }
                    }
                }
            }
            case 200: {
                $message = self::parseResponse($exception->getMessage()?$exception->getMessage():'Ok!');
                break;
            }
            case 400: {
                $message = ($exception->getCode())?$exception->getMessage():'Bad Request!';
                break;
            }
            case 401: {
                $message = ($exception->getCode())?$exception->getMessage():'Unauthorized!';
                break;
            }
            case 403:
            {
                $message = ($exception->getCode()) ? $exception->getMessage() : 'Request not allowed!';
                break;
            }
            case 404: {
                $message = ($exception->getCode())?$exception->getMessage():'Route not found!';
                break;
            }
            case 405: {
                $message = ($exception->getCode())?$exception->getMessage():'Method not allowed!';
                break;
            }
            case 500: {
                $message = self::parseResponse($exception->getMessage()?$exception->getMessage():'Internal Server Error!');
                break;
            }
            default: {
                $statusCode = 500;
                $message = $exception->getMessage();
            }
        }

        $rendered = parent::render($request, $exception);
        $data = new \stdClass();
        $data->file = $exception->getFile();
        $data->line = $exception->getLine();
        $exceptionType = method_exists($exception, 'getType')?$exception->getType():'CoreException';
        $result = [
            'StatusCode' => $statusCode,
            'Message' => $message,
            'ExceptionType' => $exceptionType,
            'StackTrace' => $exception->getTraceAsString(),
            'InnerException' => ($exception->getTrace()[0])?[
                'file' => isset($exception->getTrace()[0]['file'])?$exception->getTrace()[0]['file']:'',
                'line' => isset($exception->getTrace()[0]['line'])?$exception->getTrace()[0]['line']:'',
                'class' => isset($exception->getTrace()[0]['class'])?$exception->getTrace()[0]['class']:'',
                'function' => isset($exception->getTrace()[0]['function'])?$exception->getTrace()[0]['class']:'',
                'type' => isset($exception->getTrace()[0]['type'])?$exception->getTrace()[0]['class']:'',
            ]:'',

            'Data' => $data,
            'HelpLink' => null,
            'HResult'=> 8388608,
            'Source' => 'Core'
        ];
        Logger::log($result, $request);
        unset($result['StatusCode']);
        if(!($request->has('debug') && env('APP_DEBUG'))) {
//            unset($result['StackTrace']);
//            unset($result['InnerException']);
            unset($result['Data']);
            unset($result['HelpLink']);
            unset($result['HResult']);
        }
        return response()->json([
            'StatusCode' => $statusCode,
            'StatusMessage' => 'ERROR',
            'Result' => $result,
        ], $statusCode);
    }

    public static function parseResponse($message) {
        $match = [];
        preg_match("/\[SQL Server\](.*) \(/", $message, $match);
        if(isset($match[1]) && !empty($match[1])) {
            $message = $match[1];
        }
        return $message;
    }

}
