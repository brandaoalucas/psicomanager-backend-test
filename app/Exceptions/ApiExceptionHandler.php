<?php

namespace App\Exceptions;

use App\BoundedContext\Iam\Application\Exception\UserEmailAlreadyExistsException;
use App\BoundedContext\Iam\Application\Exception\UserUsernameAlreadyExistsException;
use App\BoundedContext\Task\Application\Exception\TaskNotFoundException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class ApiExceptionHandler
{
    /**
     * Map of exception classes to their handler methods
     */
    public static array $handlers = [
        // Framework Exceptions
        AuthenticationException::class => 'handleAuthenticationException',
        AccessDeniedHttpException::class => 'handleAuthenticationException',
        AuthorizationException::class => 'handleAuthorizationException',
        ValidationException::class => 'handleValidationException',
        ModelNotFoundException::class => 'handleNotFoundException',
        NotFoundHttpException::class => 'handleNotFoundException',
        MethodNotAllowedHttpException::class => 'handleMethodNotAllowedException',
        HttpException::class => 'handleHttpException',
        QueryException::class => 'handleQueryException',

        // Domain Exceptions (Your DDD Exceptions)
        UserEmailAlreadyExistsException::class => 'handleDomainConflictException',
        UserUsernameAlreadyExistsException::class => 'handleDomainConflictException',
        TaskNotFoundException::class => 'handleDomainNotFoundException',
    ];

    // ===============================================
    // DOMAIN EXCEPTION HANDLERS (Your Business Logic)
    // ===============================================

    /**
     * Handle domain conflicts (422 - Business Logic Violations)
     */
    public function handleDomainConflictException(
        UserEmailAlreadyExistsException|UserUsernameAlreadyExistsException $e,
        Request $request
    ): JsonResponse {
        $this->logDomainException($e, 'Domain conflict detected');

        return response()->json([
            'message' => $e->getMessage()
        ], 422);
    }

    /**
     * Handle domain not found exceptions (404)
     */
    public function handleDomainNotFoundException(
        TaskNotFoundException $e,
        Request $request
    ): JsonResponse {
        $this->logDomainException($e, 'Domain resource not found');

        return response()->json([
            'message' => $e->getMessage()
        ], 404);
    }

    // ===============================================
    // FRAMEWORK EXCEPTION HANDLERS
    // ===============================================

    /**
     * Handle authentication exceptions (401)
     */
    public function handleAuthenticationException(
        AuthenticationException|AccessDeniedHttpException $e,
        Request $request
    ): JsonResponse {
        $this->logException($e, 'Authentication failed');

        return response()->json([
            'message' => 'Credenciais de autenticação necessárias.'
        ], 401);
    }

    /**
     * Handle authorization exceptions (403)
     */
    public function handleAuthorizationException(
        AuthorizationException $e,
        Request $request
    ): JsonResponse {
        $this->logException($e, 'Authorization failed');

        return response()->json([
            'message' => 'Acesso negado.'
        ], 403);
    }

    /**
     * Handle validation exceptions (422)
     */
    public function handleValidationException(
        ValidationException $e,
        Request $request
    ): JsonResponse {
        $this->logException($e, 'Validation failed');

        return response()->json([
            'message' => 'Os dados fornecidos são inválidos.',
            'errors' => $e->errors()
        ], 422);
    }

    /**
     * Handle not found exceptions (404)
     */
    public function handleNotFoundException(
        ModelNotFoundException|NotFoundHttpException $e,
        Request $request
    ): JsonResponse {
        $this->logException($e, 'Resource not found');

        $message = $e instanceof ModelNotFoundException
            ? 'Recurso não encontrado.'
            : 'Rota não encontrada.';

        return response()->json([
            'message' => $message
        ], 404);
    }

    /**
     * Handle method not allowed exceptions (405)
     */
    public function handleMethodNotAllowedException(
        MethodNotAllowedHttpException $e,
        Request $request
    ): JsonResponse {
        $this->logException($e, 'Method not allowed');

        return response()->json([
            'message' => "O método {$request->method()} não é permitido para esta rota."
        ], 405);
    }

    /**
     * Handle general HTTP exceptions
     */
    public function handleHttpException(HttpException $e, Request $request): JsonResponse
    {
        $this->logException($e, 'HTTP exception occurred');

        return response()->json([
            'message' => $e->getMessage() ?: 'Erro HTTP.'
        ], $e->getStatusCode());
    }

    /**
     * Handle database query exceptions (500)
     */
    public function handleQueryException(QueryException $e, Request $request): JsonResponse
    {
        $this->logException($e, 'Database query failed', ['sql' => $e->getSql()]);

        // Handle specific database constraint violations
        $errorCode = $e->errorInfo[1] ?? null;

        switch ($errorCode) {
            case 1451: // Foreign key constraint violation
                return response()->json([
                    'message' => 'Não é possível excluir este recurso pois está sendo referenciado.'
                ], 409);

            case 1062: // Duplicate entry
                return response()->json([
                    'message' => 'Um registro com estas informações já existe.'
                ], 409);

            default:
                return response()->json([
                    'message' => 'Erro interno do servidor.'
                ], 500);
        }
    }

    // ===============================================
    // UTILITY METHODS
    // ===============================================

    /**
     * Log domain exceptions (business logic errors)
     */
    private function logDomainException(Throwable $e, string $message, array $context = []): void
    {
        // Domain exceptions are usually warnings, not errors
        $logContext = array_merge([
            'exception' => get_class($e),
            'message' => $e->getMessage(),
            'route' => request()->route()?->getName(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'user_id' => auth()->id(),
        ], $context);

        Log::warning($message, $logContext);
    }

    /**
     * Log framework/system exceptions
     */
    private function logException(Throwable $e, string $message, array $context = []): void
    {
        $logContext = array_merge([
            'exception' => get_class($e),
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ], $context);

        // Different log levels based on exception type
        $logLevel = $this->determineLogLevel($e);
        Log::{$logLevel}($message, $logContext);
    }

    /**
     * Determine appropriate log level for exception
     */
    private function determineLogLevel(Throwable $e): string
    {
        return match (true) {
            $e instanceof ValidationException,
            $e instanceof AuthenticationException,
            $e instanceof AuthorizationException,
            $e instanceof NotFoundHttpException,
            $e instanceof ModelNotFoundException => 'info',

            $e instanceof QueryException => 'error',

            default => 'warning'
        };
    }
}
