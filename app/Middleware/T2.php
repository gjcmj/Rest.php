<?php namespace App\Middleware;

use Closure;
use Rest\Http\Request;

class T2 {

    public function handle(Request $request, Closure $next) {

        echo '2';
        $response = $next($request);

        return $response;
    }
}
