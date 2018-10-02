<?php

namespace App\Http\Middleware;

use Closure;
use App\Sensor;

class AuthenticateArduinoSensor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if( ! $request->headers->has('Authorization')){
            return response('Unauthorized.', 401);
        }

        $token = $request->header('Authorization');
        $sensor = Sensor::where('access_key', $token)->first();

        if ($sensor === null) {
            return response('Unauthorized.', 401);
        }

        return $next($request);
    }
}
