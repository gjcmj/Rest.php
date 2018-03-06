<?php namespace App\Middleware;

use Closure;
use Rest\Http\Request;

class T3 {

    public function handle(Request $request, Closure $next) {

        echo '3';
        echo $request->params('a');
        $response = $next($request);

        return $response;
    }
}
