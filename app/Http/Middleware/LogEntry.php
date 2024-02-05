<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogEntry
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);


        if(app()->environment('local')) {
            $log = [
                "USER" => auth()->user(),
                "URI" => $request->getUri(),
                "METHOD" => $request->getMethod(),
                "RESPONSE" => $response->getStatusCode()
            ];
            if($log['RESPONSE'] >= 500) {
                Log::warning(json_encode($log));
            }
            else{
                Log::info(json_encode($log));
            }
        }

        return $response;
    }
}
