<?php

namespace App\Http\Middleware;
use Config;
use Closure;

class SetAuthProviderUser
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
        config(['auth.defaults.guard' => 'api']);
        config(['auth.providers.users.model' => \App\Models\User::class]);
        
        Config::set('jwt.user' , "App\Models\User");
        Config::set('auth.providers.users.model', \App\Models\User::class);

        return $next($request);  
    }
}
