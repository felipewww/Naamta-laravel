<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
//        dd(Auth::user());
//        dd(Auth::check());
//        dd($request->all());

        if (Auth::guard($guard)->check()) {
            return redirect('/home');
        }

//        dd(Auth::check('api'));
        return $next($request);
    }
}
