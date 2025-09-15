<?php

namespace App\Exceptions;

use App\BoundedContext\Iam\Application\Exception\UserEmailAlreadyExistsException;
use App\BoundedContext\Iam\Application\Exception\UserUsernameAlreadyExistsException;
use App\BoundedContext\Task\Application\Exception\TaskNotFoundException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
        // Domain exceptions shouldn't be reported as they're business logic
        UserEmailAlreadyExistsException::class,
        UserUsernameAlreadyExistsException::class,
        TaskNotFoundException::class,
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
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable($this->createRender(...));
    }

    /**
     * Report or log an exception.
     */
    public function report(Throwable $e): void
    {
        if ($e instanceof IApplicationException) {
            Log::{$e->getLogLevel()}(
                $e->getMessage(),
                array_merge(
                    $this->exceptionContext($e),
                    $this->context(),
                    [
                        'exception' => get_class($e),
                        'route' => Route::current()?->uri(),
                        'args' => method_exists($e, 'getArgs') ? $e->getArgs() : []
                    ]
                )
            );
        } else {
            parent::report($e);
        }
    }

    /**
     * Create appropriate response for different exception types.
     */
    private function createRender(Throwable $e, $request): mixed
    {
        // Only return JSON responses for API routes or AJAX requests
        if (!$this->shouldReturnJson($request, $e)) {
            return null; // Let Laravel handle non-API routes
        }

        return match (true) {
            // Authentication & Authorization
            $e instanceof AuthenticationException => $this->notAuthenticated(),
            $e instanceof AuthorizationException => $this->forbidden(),

            // Routing
            $e instanceof RouteNotFoundException,
            $e instanceof NotFoundHttpException => $this->notFound(),

            // Validation
            $e instanceof ValidationException => $this->invalidRequestInput($e),

            // Domain-specific Business Logic Errors (422)
            $e instanceof UserEmailAlreadyExistsException => $this->conflictError($e, 'USER_EMAIL_EXISTS'),
            $e instanceof UserUsernameAlreadyExistsException => $this->conflictError($e, 'USER_USERNAME_EXISTS'),

            // Resource Not Found (404)
            $e instanceof TaskNotFoundException => $this->resourceNotFound($e, 'TASK_NOT_FOUND'),

            // Generic Application Exceptions (400)
            $e instanceof IApplicationException => $this->badRequest($e),

            // System Errors (500)
            default => $this->internalError($e),
        };
    }

    /**
     * Determine if we should return JSON response.
     */
    private function shouldReturnJson($request, Throwable $e): bool
    {
        return $request->expectsJson() ||
               $request->is('api/*') ||
               $request->is('*/api/*') ||
               $request->header('Accept') === 'application/json';
    }

    // ===============================================
    // HTTP STATUS CODE HANDLERS
    // ===============================================

    /**
     * 401 - Unauthenticated
     */
    private function notAuthenticated(): JsonResponse
    {
        return response()->json([
            'error' => [
                'code' => 'UNAUTHENTICATED',
                'message' => 'Credenciais de autenticação necessárias.',
                'http_code' => 401
            ]
        ], 401);
    }

    /**
     * 403 - Forbidden
     */
    private function forbidden(): JsonResponse
    {
        return response()->json([
            'error' => [
                'code' => 'FORBIDDEN',
                'message' => 'Acesso negado. Você não tem permissão para acessar este recurso.',
                'http_code' => 403
            ]
        ], 403);
    }

    /**
     * 404 - Not Found
     */
    private function notFound(): JsonResponse
    {
        return response()->json([
            'error' => [
                'code' => 'ROUTE_NOT_FOUND',
                'message' => 'Rota não encontrada.',
                'http_code' => 404
            ]
        ], 404);
    }

    /**
     * 404 - Resource Not Found
     */
    private function resourceNotFound(IApplicationException $e, string $code): JsonResponse
    {
        return response()->json([
            'error' => [
                'code' => $code,
                'message' => $e->getMessage(),
                'http_code' => 404
            ]
        ], 404);
    }

    /**
     * 422 - Validation Failed
     */
    private function invalidRequestInput(ValidationException $exception): JsonResponse
    {
        return response()->json([
            'error' => [
                'code' => 'VALIDATION_FAILED',
                'message' => 'Os dados fornecidos são inválidos.',
                'http_code' => 422,
                'details' => $exception->validator->getMessageBag()
            ]
        ], 422);
    }

    /**
     * 422 - Business Logic Conflict (like duplicate email)
     */
    private function conflictError(IApplicationException $e, string $code): JsonResponse
    {
        return response()->json([
            'error' => [
                'code' => $code,
                'message' => $e->getMessage(),
                'http_code' => 422
            ]
        ], 422);
    }

    /**
     * 400 - Bad Request (Generic Application Exception)
     */
    private function badRequest(IApplicationException $e): JsonResponse
    {
        $response = [
            'error' => [
                'code' => 'BAD_REQUEST',
                'message' => $e->getMessage(),
                'http_code' => 400
            ]
        ];

        // Only include exception details in testing/development
        if (App::environment(['testing', 'local'])) {
            $response['error']['exception'] = get_class($e);

            if (method_exists($e, 'getArgs')) {
                $response['error']['args'] = $e->getArgs();
            }
        }

        return response()->json($response, 400);
    }

    /**
     * 500 - Internal Server Error
     */
    private function internalError(Throwable $e): JsonResponse
    {
        $response = [
            'error' => [
                'code' => 'INTERNAL_SERVER_ERROR',
                'message' => 'Um erro inesperado ocorreu. Tente novamente em alguns instantes.',
                'http_code' => 500
            ]
        ];

        // Only include detailed error info in development
        if (App::environment(['local', 'testing'])) {
            $response['error']['debug'] = [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ];

            // Include stack trace only in local development
            if (App::environment('local')) {
                $response['error']['debug']['trace'] = $e->getTraceAsString();
            }
        }

        return response()->json($response, 500);
    }
}
