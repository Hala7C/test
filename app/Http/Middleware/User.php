<?php

namespace App\Http\Middleware;
use App\Providers\RouteServiceProvider;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class User
{

    public function handle(Request $request, Closure $next)
    {

          if(Auth::user()->role === 'user')
        {
            return $next($request);
        }
        else
        {

         return redirect()->route('logout1');

        }
        return $next($request);
    }
}
