<?php

namespace App\Http\Middleware;

use Closure;


class LimitRequestSize
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $size=(int) $_SERVER['CONTENT_LENGTH'];
        $size_in_mb=$size/(1024*1024);
        if($size_in_mb > 10){
            // return response()->json($size,210);
             return response('request size must be less than 10MB',410);
        }else{
            $next($request);
        }
    }
    }

