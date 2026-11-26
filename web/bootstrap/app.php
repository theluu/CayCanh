<?php

use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\HandleRedirects;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(prepend: [
            HandleRedirects::class,
        ], append: [
            HandleInertiaRequests::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        $exceptions->respond(function (Response $response, Throwable $exception, Request $request) {
            $status = $response->getStatusCode();
            $isPublicPage = ! $request->is('admin', 'admin/*', 'livewire*', 'filament*');
            $renderable = in_array($status, [403, 404, 419, 429, 503], true) || ($status === 500 && ! app()->hasDebugModeEnabled());

            if ($isPublicPage && $renderable && ! $request->expectsJson()) {
                Inertia::share(app(HandleInertiaRequests::class)->share($request));

                return Inertia::render('Error', ['status' => $status])
                    ->toResponse($request)
                    ->setStatusCode($status);
            }

            return $response;
        });
    })->create();
