<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApiTokenIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('X-API-TOKEN');
        $validToken = env('CANDIDATE_API_TOKEN');

        if (!$validToken || $token !== $validToken) {
            return response()->json(['message' => 'Unauthorized. Invalid API Token.'], 401);
        }

        return $next($request);
    }
}
