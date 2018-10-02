<?php

namespace App\Http\Middleware;

use Log;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RequestLogMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        /*Log::info("Request logged\n-----" .
            sprintf("\n%s", (string) $request) . "\n" .
            print_r($request->all(), true) . "\n"
        );*/

        return $next($request);
    }

    /**
     * Store request/response in log.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Response $response
     */
    // public function terminate($request, $response)
    // {
    //     Log::info('requests', [
    //         'request' => $request->all(),
    //         'response' => $response
    //     ]);
    // }
}
