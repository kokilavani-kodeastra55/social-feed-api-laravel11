<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Support\Api\ApiExceptionRenderer;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (ValidationException $exception, Request $request) {
            if (! ApiExceptionRenderer::shouldRenderApi($request)) {
                return null;
            }

            return ApiExceptionRenderer::validation($exception);
        });

        $exceptions->render(function (AuthenticationException $exception, Request $request) {
            if (! ApiExceptionRenderer::shouldRenderApi($request)) {
                return null;
            }

            return ApiExceptionRenderer::unauthenticated();
        });

        $exceptions->render(function (NotFoundHttpException $exception, Request $request) {
            if (! ApiExceptionRenderer::shouldRenderApi($request)) {
                return null;
            }

            return ApiExceptionRenderer::notFound();
        });

        $exceptions->render(function (\Throwable $exception, Request $request) {
            if (! ApiExceptionRenderer::shouldRenderApi($request)) {
                return null;
            }

            report($exception);

            return ApiExceptionRenderer::serverError();
        });
    })->create();
