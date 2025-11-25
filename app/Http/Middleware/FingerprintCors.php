<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class FingerprintCors
{
    public function handle(Request $request, Closure $next)
    {
        $headers = [
            'Access-Control-Allow-Origin'      => '*',
            'Access-Control-Allow-Methods'     => 'POST, GET, OPTIONS',
            'Access-Control-Allow-Headers'     => 'Content-Type, X-Requested-With, X-CSRF-TOKEN',
            'Access-Control-Max-Age'           => '86400',
            'Access-Control-Allow-Credentials' => 'false',
        ];

        // Handle preflight request
        if ($request->getMethod() === "OPTIONS") {
            return response()->json('OK', 200, $headers);
        }

        // Normal flow
        $response = $next($request);

        foreach ($headers as $key => $value) {
            $response->headers->set($key, $value);
        }

        return $response;
    }
}
