<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;

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
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        if ($request->wantsJson() && !($e instanceof ValidationException))
        {
            $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;

            if ($e instanceof ModelNotFoundException)
            {
                $statusCode = Response::HTTP_NOT_FOUND;
            }

            if ($e instanceof HttpException)
            {
                $statusCode = $e->getStatusCode();
            }

            $message = Response::$statusTexts[$statusCode];

            $content = ['message' => $message];

            if (env('APP_DEBUG')) {
                $content = array_merge($content,  [
                                'file'    => $e->getFile(),
                                'line'    => $e->getLine(),
                                'trace'   => $e->getTrace(),
                            ]);
            }

            return response($content, $statusCode);
        }

        return parent::render($request, $e);
    }
}
