<?php namespace App\Middleware;

use Closure;
use Rest\Http\Request;
use App\Config\Errors;

class Test {

    public function handle(Request $request, Closure $next) {

        // coding

        return $next($request);
    }
}
