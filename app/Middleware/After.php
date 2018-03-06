<?php namespace App\Middleware;

use Closure;
use Rest\Http\Request;

class After {

    public function handle(Request $request, Closure $next) {

        $response = $next($request);

        // 执行动作

        return $response;
    }
}
