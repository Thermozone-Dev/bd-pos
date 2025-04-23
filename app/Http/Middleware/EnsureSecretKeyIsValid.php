<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSecretKeyIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $request->validate([
            'secret_key' => 'required|string',
        ]);

        if ($request->secret_key != config('api.secret_key')) {
            $_err = ['error' => 'Invalid Secret Key',];
            return response()->json($_err, 400);
        }

        return $next($request);
    }
}
