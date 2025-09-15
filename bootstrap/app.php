<?php

use App\Exceptions\ApiExceptionHandler;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        // Configure JSON responses for API routes
        $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e) {
            return $request->expectsJson() ||
                   $request->is('api/*') ||
                   $request->is('users*') ||
                   $request->is('tasks*') ||
                   $request->is('logs*') ||
                   $request->header('Accept') === 'application/json';
        });

        // Global exception rendering with organized handler
        $exceptions->render(function (Throwable $e, Request $request) {
            // Get the exception class name
            $className = get_class($e);

            // Get our custom handlers mapping
            $handlers = ApiExceptionHandler::$handlers;

            // Check if we have a specific handler for this exception
            if (array_key_exists($className, $handlers)) {
                $method = $handlers[$className];
                $apiHandler = new ApiExceptionHandler();
                return $apiHandler->$method($e, $request);
            }

            // Fallback to default error response for unhandled exceptions
            return response()->json([
                'message' => app()->environment('local')
                    ? $e->getMessage()
                    : 'Erro interno do servidor.',
                // Include debug info only in development
                'debug' => app()->environment('local') ? [
                    'exception' => basename(str_replace('\\', '/', get_class($e))),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ] : null
            ], 500);
        });

        // Configure what exceptions should NOT be reported to logs
        $exceptions->dontReport([
            \Illuminate\Auth\AuthenticationException::class,
            \Illuminate\Validation\ValidationException::class,
            \Illuminate\Auth\Access\AuthorizationException::class,
            // Domain exceptions (they're business logic, not system errors)
            \App\BoundedContext\Iam\Application\Exception\UserEmailAlreadyExistsException::class,
            \App\BoundedContext\Iam\Application\Exception\UserUsernameAlreadyExistsException::class,
            \App\BoundedContext\Task\Application\Exception\TaskNotFoundException::class,
        ]);

    })->create();
