<?php

namespace App\Http\Middleware;

use Closure;

class AdminAuth
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
        if(!empty(auth()->guard('admin')->id()))
        {
            return $next($request);
        }
        else 
        {
            return redirect("admin/login")->with('status', 'Please Login to access admin area');
        }
    }
}
