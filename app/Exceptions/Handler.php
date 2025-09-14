<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
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
        AuthenticationException::class,
        ValidationException::class,
        AuthorizationException::class,
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
        $this->reportable(function (Throwable $e) {});

        $this->renderable($this->createRender(...));
    }

    public function report(Throwable $e)
    {
        if ($e instanceof IApplicationException) {
            Log::{$e->getLogLevel()}(
                $e->getMessage(),
                array_merge(
                    $this->exceptionContext($e),
                    $this->context(),
                    ['exception' => $e, 'route' => Route::current()?->uri()]
                )
            );
        } else {
            parent::report($e);
        }
    }

    private function createRender(Throwable $e, $request): mixed
    {
        return match (true) {
            $e instanceof RouteNotFoundException  => $this->notFound(),
            $e instanceof AuthenticationException => $this->notAuthenticated(),
            $e instanceof AuthorizationException  => $this->forbidden(),
            $e instanceof ValidationException     => $this->invalidRequestInput($e),
            $e instanceof IApplicationException   => $this->badRequest($e),
            default                               => $this->internalError($e),
        };
    }

    private function notAuthenticated(): JsonResponse
    {
        return response()->json([
            'error' => 'unauthenticated',
        ], 401);
    }

    private function forbidden(): JsonResponse
    {
        return response()->json([
            'error' => 'forbidden',
        ], 403);
    }

    private function notFound(): JsonResponse
    {
        return response()->json([
            'error' => 'not_found',
        ], 404);
    }

    private function internalError(Throwable $e): JsonResponse
    {
        return response()->json([
            'error'      => 'internal_error',
            'message'    => 'Um erro inesperado ocorreu. Tente novamente!',
            'exception'  => get_class($e),
            'stacktrace' => $e->getMessage(),
        ], 500);
    }

    private function invalidRequestInput(ValidationException $exception): JsonResponse
    {
        return response()->json([
            'error'   => 'validation_failed',
            'message' => $exception->validator->getMessageBag(),
        ], 422);
    }

    private function badRequest(IApplicationException $e)
    {
        if (App::environment() == 'testing') {
            return response()->json([
                'error'     => 'bad_request',
                'exception' => get_class($e),
                'message'   => $e->getMessage(),
            ], 400);
        }

        return response()->json([
            'error'   => 'bad_request',
            'message' => $e->getMessage(),
        ], 400);
    }
}
