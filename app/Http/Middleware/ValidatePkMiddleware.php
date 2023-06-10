<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Store;

class ValidatePkMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // $id = $request->route('id');

        $user = Store::where('id', '=', $request->route('id'))->first();
        if ($user === null) {
            dd('なし!');
           // user doesn't exist
        }

        // dd($user);
        $input = "Testミドルウェア";
        $request->merge(['content'=>$input]);
        // dd($request);
        

        return $next($request);
    }
}
