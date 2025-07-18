<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $e, Request $request){
            // Http exceptions se defaultne nereportuji, takze je zaloguju explicitne

            $className = class_basename($e::class);
            Log::error($e->getMessage(), [
                'exception' => $className,
                'code' => $e->getCode(),
                'source' => 'Line: ' . $e->getLine() . ', File: ' . $e->getFile()
            ]);

            // Detekce Livewire požadavku
            $wantsJson = ($request->header('X-Livewire') === 'true') || $request->expectsJson() || $request->isXmlHttpRequest();

            // Pokud API nebo Livewire – vrať JSON
            if ($wantsJson) {
                return response()->json([
                    'message' => 'Nastala chyba.',
                    'error' => config('app.debug') ? $e->getMessage() : 'Zkuste to prosím později.',
                ], 500);
            }

            if($e instanceof AuthenticationException)
            {
                return redirect('/login');
            }

            return response()->view('error', [
                'exception' => $e,
            ], 500);
        });
    })->create();
