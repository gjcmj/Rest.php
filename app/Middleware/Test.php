<?php namespace App\Middleware;

use Closure;
use Rest\Http\Request;
use App\Config\Errors;

class Test {

    public function handle(Request $request, Closure $next) {

        echo 'test';

        $response = $next($request);

        return $response;
    }
}
