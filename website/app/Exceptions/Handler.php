<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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

    public function render($request, Throwable $e)
    {
        // dd($e);
        if ($request->expectsJson() || $e instanceof QueryException || $e instanceof ValidationException || $e instanceof UnauthorizedException || $e instanceof AuthorizationException || $e instanceof AuthenticationException || $e instanceof ResourceNotFound) {
            // Handle API errors
            return $this->handleApiException($e);
        }

        return parent::render($request, $e);
    }

    private function handleApiException(Throwable $e)
    {
        // Customize the API error response for specific exceptions
        $statusCode = method_exists($e, 'getCode') ? $e->getCode() : 500;

        Log::error('Custom Error Handler => ' . $e->getMessage());

        if ($e instanceof ValidationException) {
            return response()->json([
                'message' => 'Validation errors',
                'errors' => $e->errors(),
                'status_code' => 422
            ], 422);
        } elseif ($e instanceof UnauthorizedException || $e instanceof ResourceNotFound) {
            return response()->json([
                'message' => $e->getMessage(),
                'status_code' => $statusCode
            ], $statusCode);
        } elseif ($e instanceof AuthorizationException || $e instanceof AuthenticationException) {
            return response()->json([
                'message' => 'Request is not available to you',
                'status_code' => 403
            ], 403);
        } else {
            return response()->json([
                'error' => [
                    'message' => 'Something went wrong.',
                    'status_code' => $statusCode,
                ]
            ], $statusCode);
        }
    }
}
