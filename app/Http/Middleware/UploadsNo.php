<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class UploadsNo
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        $count=User::find($user->id)->documents()->count();
        $No= Config::get('app.count.number');
        if($count < $No){
            return $next($request);
        }
        else {
            $error = ['message' => 'You pass the allowed number of uploads!!you have already '.$No.' files'];
            return response()->json($error, 404);
        }
    }
}
