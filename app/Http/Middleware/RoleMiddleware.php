<?php

namespace App\Http\Middleware;

use Closure;

class RoleMiddleware{
    public function handle($request, Closure $next, ...$roles){

        foreach($roles as $role){
            if(auth()->payload()->get('role') === $role){
                return $next($request);
            }
        }
        
        return response()->json([
            'status'=>301,
            'message'=>'forbidden'
        ]);
    }
}