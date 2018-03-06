<?php namespace App\Middleware;

use Closure;
use Rest\Http\Request;

class Before {

    public function handle(Request $request, Closure $next) {

        // 执行动作
        
        return $next($request);
    }
}
