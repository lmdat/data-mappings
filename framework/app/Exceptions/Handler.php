<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
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
        // if($exception instanceof NotFoundHttpException){
        //     return response()->json(['error' => 'NOT_FOUND_HTTP_EXCEPTION', 'message' => 'Url not found'], 401);
        // }

                
        // if ($exception instanceof \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException) {
        //     switch (get_class($exception->getPrevious())) {
        //         case \Tymon\JWTAuth\Exceptions\TokenExpiredException::class:
        //             return response()->json([
        //                 'success' => false,
        //                 'error' => 'TOKEN_HAS_EXPIRED'
        //             ], $exception->getStatusCode());

        //         case \Tymon\JWTAuth\Exceptions\TokenInvalidException::class:
        //             return response()->json([
        //                 'success' => false,
        //                 'error' => 'TOKEN_IS_INVALID'
        //             ], $exception->getStatusCode());

        //         case \Tymon\JWTAuth\Exceptions\TokenBlacklistedException::class:
        //             return response()->json([
        //                 'success' => false,
        //                 'error' => 'TOKEN_IS_BLACKLISTED'
        //             ], $exception->getStatusCode());
                               
        //         default:
        //             return response()->json([
        //                 'success' => false,
        //                 'error' => 'TOKEN_IS_NOT_PROVIDED'
        //             ], $exception->getStatusCode());
        //             break;
        //     }
        // }

        
        if($exception instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
            return response()->json([
                'success' => false,
                'error' => 'TOKEN_HAS_EXPIRED'
            ], 401);
        }        

        else if($exception instanceof \Tymon\JWTAuth\Exceptions\TokenBlacklistedException){
            return response()->json([
                'success' => false,
                'error' => 'TOKEN_IS_IN_BLACKLISTED'
            ], 401);
        }
        
        else if($exception instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
            return response()->json([
                'success' => false,
                'error' => 'TOKEN_IS_INVALID'
            ], 401);
        }
        
        else if($exception instanceof \Tymon\JWTAuth\Exceptions\JWTException){
            return response()->json([
                'success' => false,
                'error' => 'TOKEN_IS_NOT_PROVIDED'
            ], 401);
        }

        return parent::render($request, $exception);
    }

    // /**
    //  * Convert an authentication exception into an unauthenticated response.
    //  *
    //  * @param  \Illuminate\Http\Request  $request
    //  * @param  \Illuminate\Auth\AuthenticationException  $exception
    //  * @return \Illuminate\Http\Response
    //  */
    // protected function unauthenticated($request, \Illuminate\Auth\AuthenticationException $exception)
    // {
    //     if ($request->expectsJson()) {
    //         return response()->json(['error' => 'Unauthenticated.'], 401);
    //     }

    //     return redirect()->guest('login');
    // }
}
