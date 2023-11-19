<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {

        if ($request->expectsJson()) {
            return $this->handleJsonException($exception);
        }

        return parent::render($request, $exception);
    }

    private function handleJsonException($exception)
    {
        $statusCode = 500;
        $responses = [
            'status'  => 'error',
            'message' => $exception->getMessage(),
            'data' => null
        ];

        /**
         * Laravel validation exception render.
         */
        if ($exception instanceof ValidationException) {
            $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY;
            $responses['message']   = 'validation error';
            $errors = $exception->validator->errors();
            $msgbag = $errors;
            $errors = [];
            foreach($msgbag->messages() as $key => $value) $errors[] = [ 'attribute' => $key, 'text' => $value[0]];

            $responses['errors'] = $errors;
        }

        if (
            $exception instanceof NotFoundHttpException
        ) {
            $statusCode = Response::HTTP_NOT_FOUND;
            $responses['message'] = 'Data not found.';
        }

        if (
            $exception instanceof ModelNotFoundException
        ) {
            $statusCode = Response::HTTP_NOT_FOUND;
            $responses['message'] = 'Data not found.';
        }

        if (
            $exception instanceof AuthorizationException ||
            $exception instanceof AuthenticationException
        ) {
            $statusCode = Response::HTTP_UNAUTHORIZED;
            $responses['message'] = $exception->getMessage();
        }

        if (
            $exception instanceof AuthorizationException ||
            $exception instanceof AccessDeniedHttpException
        ) {
            $statusCode = Response::HTTP_FORBIDDEN;
            $responses['message'] = $exception->getMessage();
        }

        if (
            $exception instanceof MethodNotAllowedHttpException
        ) {
            $statusCode = Response::HTTP_METHOD_NOT_ALLOWED;
            $responses['message'] = $exception->getMessage();
        }

        return response()->json($responses, $statusCode);
    }
}
