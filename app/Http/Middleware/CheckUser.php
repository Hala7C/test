<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;

class CheckUser
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
        $id = $request->id;
        //  dd($request);
        $group = Group::find($id);
        $users = $group->users->pluck('id')->toArray();
        $user = Auth::user();
        if (in_array($user->id, $users)) {
            return $next($request);
        }
        $error = ['message' => 'You are denied access to this page'];
        return response()->json($error, 404);
    }
}
