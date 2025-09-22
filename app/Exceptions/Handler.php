<?php

namespace App\Exceptions;

use Dotenv\Exception\ValidationException as DotenvValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */

    protected $dontReport = [
        AuthenticationException::class,
        AuthorizationException::class,
        ModelNotFoundException::class,
        NotFoundHttpException::class,
        ValidationException::class,
    ];
    
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Throwable $exception
     * @return \Illuminate\Http\JsonResponse
     */
    public function render($request, Throwable $exception): JsonResponse
    {
        // Handle the exception using the centralized response formatting method
        return $this->generateErrorResponse($exception);
    }

    /**
     * Generate the error response.
     * 
     * @param Throwable $exception
     * @return JsonResponse
     */
    protected function generateErrorResponse(Throwable $exception): JsonResponse
    {
        // Determine the error type and generate a corresponding response
        switch (true) {
            case $exception instanceof ValidationException:
                return $this->validationErrorResponse($exception);

            case $exception instanceof DotenvValidationException:
                return $this->dotenvErrorResponse($exception);

            case $exception instanceof ModelNotFoundException:
                return $this->modelNotFoundResponse($exception);

            case $exception instanceof QueryException:
                return $this->databaseErrorResponse($exception);

            case $exception instanceof NotFoundHttpException:
                return $this->notFoundResponse();

            case $exception instanceof AuthenticationException:
                return $this->authenticationErrorResponse();

            case $exception instanceof AuthorizationException:
                return $this->authorizationErrorResponse();

            default:
                return $this->defaultErrorResponse($exception);
        }
    }

    /**
     * Handle validation exceptions.
     *
     * @param ValidationException $exception
     * @return JsonResponse
     */
    protected function validationErrorResponse(ValidationException $exception): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => 'Validation failed.',
            'errors' => $exception->errors(),
        ], 422);
    }

    /**
     * Handle Dotenv validation exceptions (e.g., .env validation issues).
     *
     * @param DotenvValidationException $exception
     * @return JsonResponse
     */
    protected function dotenvErrorResponse(DotenvValidationException $exception): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => 'Dotenv validation failed.',
            'details' => $exception->getMessage(),
        ], 400);
    }

    /**
     * Handle ModelNotFoundExceptions (when a model is not found).
     *
     * @param ModelNotFoundException $exception
     * @return JsonResponse
     */
    protected function modelNotFoundResponse(ModelNotFoundException $exception): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => 'The requested resource could not be found.',
        ], 404);
    }

    /**
     * Handle database query exceptions (e.g., SQL errors).
     *
     * @param QueryException $exception
     * @return JsonResponse
     */
    protected function databaseErrorResponse(QueryException $exception): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => 'A database error occurred.',
            'details' => $exception->getMessage(),
        ], 500);
    }

    /**
     * Handle NotFoundHttpException (route or resource not found).
     *
     * @return JsonResponse
     */
    protected function notFoundResponse(): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => 'The requested resource could not be found.',
        ], 404);
    }

    /**
     * Handle AuthenticationException (when user is not authenticated).
     *
     * @return JsonResponse
     */
    protected function authenticationErrorResponse(): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => 'Authentication required.',
        ], 401);
    }

    /**
     * Handle AuthorizationException (when user is not authorized).
     *
     * @return JsonResponse
     */
    protected function authorizationErrorResponse(): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => 'You are not authorized to perform this action.',
        ], 403);
    }

    /**
     * Handle generic unhandled exceptions.
     *
     * @param Throwable $exception
     * @return JsonResponse
     */
    protected function defaultErrorResponse(Throwable $exception): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => 'Something went wrong.',
            'details' => $exception->getMessage(),
        ], 500);
    }
}
