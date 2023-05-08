<?php

namespace MakeRep\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
        ApiException::class
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        $result = [];
        if(config('app.debug')){
            $traces = $exception->getTrace();
            $result['traces'] = $traces[0];
        }
        if($exception instanceof ApiException){
            $code = $exception->getCode();
            $extra = $exception->getExtra();
            $config = $this->getConfig($code);
            list($code,$msg) = $config;
            $result['errcode'] = $code;
            $result['errmsg'] = $msg;
            if($extra){
                $result['data'] = $extra;
                if(collect($extra)->has('msg')){
                    $result['errmsg'] = $extra['msg'];
                }
            }else{
                $result['data'] = [];
            }
            $status = 200;

            if($code == 4003 || $code == 4004){
                $status = 433;
            }
            return response()->json($result, $status);
        }
        if($exception instanceof ModelNotFoundException){
            $config = config("code.NOT_FOUND");
            list($code,$msg) = $config;
            $result['errcode'] = $code;
            $result['errmsg'] = $msg;
            return response()->json($result);
        }
        if($exception instanceof ValidationException && strpos('  '.$request->header('accept'),'application/json')){

            $errmsgs = $exception->validator->errors()->getMessages();
            $values = array_values($errmsgs);
            return response()->json(['errcode'=>3001,'errmsg'=>$values[0][0]]);
        }

        if($exception instanceof \Illuminate\Auth\Access\AuthorizationException){
            list($code,$msg) = config('code.UnAuthorized');
            return response()->json(['errcode'=>$code,'errmsg'=>$exception->getMessage()],200);
        }

        return parent::render($request, $exception);
    }

    private function getConfig($code){
        $config = config("code.{$code}") ? config("code.{$code}") : config("admin_code.{$code}");
        if(!$config){
            $config  = config("code.UNKNOWN_CODE");
        }

        return $config;
    }
}
