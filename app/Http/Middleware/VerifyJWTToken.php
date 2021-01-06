<?php

namespace App\Http\Middleware;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Closure;

class VerifyJWTToken
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
        $tokenErrorMsg = false;
        $code = 200;
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                $tokenErrorMsg = "Token invalid";
                $code = 400;
            } else {
                $payload = JWTAuth::parseToken()->getPayload();
                $user = auth()->authenticate($user);
                if (!$user) {
                    $tokenErrorMsg = "User not found";
                    $code = 404;
                }
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            $tokenErrorMsg = "Token expired";
            $code = 401;
        } catch (JWTException $e) {
            $tokenErrorMsg = "Token invalid";
            $code = 401;
        }
        if ($tokenErrorMsg) {
            return response()->json([ 'message' => $tokenErrorMsg], $code);
        }

        return $next($request);
    }
}
