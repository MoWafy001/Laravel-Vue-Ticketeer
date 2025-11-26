<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use App\Http\Responses\JsonResponse;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Remove session middleware from API routes since we use JWT
        $middleware->api(remove: [
            \Illuminate\Session\Middleware\StartSession::class,
        ]);

        // Force JSON responses for all API routes
        $middleware->api(prepend: [
            \App\Http\Middleware\ForceJsonResponse::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle Validation Exceptions
        $exceptions->renderable(function (ValidationException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return JsonResponse::error(
                    'Validation failed',
                    $e->errors(),
                    422
                );
            }
        });

        // Handle Authentication Exceptions
        $exceptions->renderable(function (AuthenticationException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return JsonResponse::error(
                    'Unauthenticated',
                    ['message' => $e->getMessage()],
                    401
                );
            }
        });

        // Handle Model Not Found Exceptions
        $exceptions->renderable(function (ModelNotFoundException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                $model = class_basename($e->getModel());
                return JsonResponse::error(
                    "{$model} not found",
                    ['message' => 'The requested resource was not found'],
                    404
                );
            }
        });

        // Handle Not Found HTTP Exceptions
        $exceptions->renderable(function (NotFoundHttpException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return JsonResponse::error(
                    'Not found',
                    ['message' => 'The requested endpoint was not found'],
                    404
                );
            }
        });

        // Handle Access Denied Exceptions (403)
        $exceptions->renderable(function (AccessDeniedHttpException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return JsonResponse::error(
                    'Access denied',
                    ['message' => $e->getMessage() ?: 'You do not have permission to access this resource'],
                    403
                );
            }
        });

        // Handle Method Not Allowed Exceptions (405)
        $exceptions->renderable(function (MethodNotAllowedHttpException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return JsonResponse::error(
                    'Method not allowed',
                    [
                        'message' => 'The HTTP method used is not allowed for this endpoint',
                        'allowed_methods' => $e->getHeaders()['Allow'] ?? null,
                    ],
                    405
                );
            }
        });

        // Handle Rate Limiting Exceptions (429)
        $exceptions->renderable(function (TooManyRequestsHttpException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return JsonResponse::error(
                    'Too many requests',
                    [
                        'message' => 'You have made too many requests. Please try again later.',
                        'retry_after' => $e->getHeaders()['Retry-After'] ?? null,
                    ],
                    429
                );
            }
        });

        // Handle Database Query Exceptions
        $exceptions->renderable(function (QueryException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                // In production, don't expose database details
                if (config('app.debug')) {
                    return JsonResponse::error(
                        'Database error',
                        [
                            'message' => $e->getMessage(),
                            'sql' => $e->getSql(),
                            'bindings' => $e->getBindings(),
                        ],
                        500
                    );
                }

                return JsonResponse::error(
                    'Database error',
                    ['message' => 'A database error occurred'],
                    500
                );
            }
        });

        // Handle HTTP Exceptions (400, 403, 500, etc.)
        $exceptions->renderable(function (HttpException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return JsonResponse::error(
                    $e->getMessage() ?: 'An error occurred',
                    ['message' => $e->getMessage()],
                    $e->getStatusCode()
                );
            }
        });

        // Handle All Other Exceptions
        $exceptions->renderable(function (\Throwable $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                $statusCode = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;

                // In production, don't expose detailed error messages
                if (config('app.debug')) {
                    return JsonResponse::error(
                        'An error occurred',
                        [
                            'message' => $e->getMessage(),
                            'exception' => get_class($e),
                            'file' => $e->getFile(),
                            'line' => $e->getLine(),
                            'trace' => $e->getTraceAsString(),
                        ],
                        $statusCode
                    );
                }

                return JsonResponse::error(
                    'An error occurred',
                    ['message' => 'Internal server error'],
                    500
                );
            }
        });
    })->create();
