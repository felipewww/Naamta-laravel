<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
        AuthenticationException::class
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
        $data = [
            'message'   => $exception->getMessage(),
            'code'      => $exception->getCode(),
            'file'      => $exception->getFile(),
            'line'      => $exception->getLine(),
        ];

        if (app('env') == 'local') {
            return parent::report($exception);
        }else{
            //23000 - duplicate email (unique key) on register
            if ( $exception->getCode() == '23000' )
            {
                return true;
            }

            return response()->view('errors.template', $data, (int)$exception->getCode());
        }

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
        $data = [
            'message'   => $exception->getMessage(),
            'code'      => $exception->getCode(),
            'file'      => $exception->getFile(),
            'line'      => $exception->getLine(),
        ];

        if (app('env') == 'local') {
            return parent::render($request, $exception);
        }else{

            //23000 - duplicate email (unique key) on register
            if ( $exception->getCode() == '23000' )
            {
                //return parent::render($request, $exception);
                return;
            }

            return response()->view('errors.template', $data, (int)$exception->getCode());
        }
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest(route('login'));
    }
}
