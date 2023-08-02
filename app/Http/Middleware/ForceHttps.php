<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceHttps
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->secure() && \App::environment(['production'])) 
        {
            return redirect()->secure($request->getRequestUri());
        }
        return $next($request);

        // ELB(EC2ロードバランサを経由する場合)
        // if (\App::environment(['production']) && $_SERVER["HTTP_X_FORWARDED_PROTO"] != 'https') 
        // {
        //     return redirect()->secure($request->getRequestUri());
        // }
        // return $next($request);
    }
}
