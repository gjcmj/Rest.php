<?php namespace Rest;

use Closure;

/**
 * Router
 * 
 * @package Rest
 * @author ky
 */
class Router {

    /**
     * Default Placeholder
     *
     * @var array
     */
    protected $placeholders = [
        ':any' => '[^/]+',
        ':num' => '[0-9]+',
        ':all' => '.*'
    ];

    /**
     * Routers
     *
     * @var array
     */
    protected $routes = [];

    /**
     * Callback
     *
     * @var array
     */
    protected $callbacks = [];

    /**
     * Method
     *
     * @var array
     */
    protected $methods = [];

    /**
     * All of the short-hand keys for middlewares.
     *
     * @var array
     */
    protected $middleware = [];

    /**
     * All of the middleware groups
     * 
     * @var array
     */
    protected $middlewareGroups = [];


    /**
     * Middleware stack
     *
     * @var array
     */
    protected $stack = [];

    /**
     * Middleware group stack
     *
     * @var array
     */
    protected $groupStack = [];

    /**
     * Construct
     *
     * @param array $placeholders
     * @param array $middleware
     * @param array $middlewareGroups
     */
    public function __construct(array $placeholders, array $middleware, array $middlewareGroups) {
        $this->addPlaceholders($placeholders);

        $this->middleware = $middleware;
        $this->middlewareGroups = $middlewareGroups;
    }

    /**
     * Request Method:
     * - GET
     * - POST
     * - PUT
     * - DELETE
     * - OPTIONS
     *
     * @return mixed
     */
    public function __call($method, $params) {
        if($method == 'middleware') {
            return $this->middleware($params);
        }

        if($method == 'match') {
            $uri = $params[1];
            $methods = array_map('strtoupper', $params[0]);
            $callback = $params[2];
        } else {
            $uri = $params[0];
            $methods = strtoupper($method);
            $callback = $params[1];
        }
        array_push($this->routes, $uri);
        array_push($this->methods, $methods);
        array_push($this->callbacks, $callback);

        if(!empty($this->groupStack)) {
            $this->stack[count($this->routes) - 1] = $this->groupStack;
        }

        return $this;
    }

    /**
     * Handle middleware
     *
     * @param array $params
     * @return void
     */
    private function middleware(array $params) {
        $arr = [];
        foreach($params as $key) {
            isset($this->middleware[$key]) && array_push($arr, $this->middleware[$key]);
        }

        $index = count($this->routes) - 1;
        $this->stack[$index] = empty($this->groupStack) ? $arr : array_merge($this->groupStack, $arr);
    }

    /**
     * Create a route group with shared attributes
     *
     * @param array $attr
     * @param Closure $routes
     * @return void
     */
    public function group(array $attr, Closure $routes) {
        $arr = [];

        foreach($attr as $key) {
            array_push($arr, $this->middlewareGroups[$key] ?? []);
        }

        $this->groupStack = array_merge(...$arr);

        $routes($this);

        $this->groupStack = [];
    }

    /**
     * Merge Placeholder
     *
     * @param array $placeholders
     * @return void
     */
    public function addPlaceholders(array $placeholders) {
        $this->placeholders = array_merge($this->placeholders, $placeholders);
    }

    /**
     * Dispatch
     * 正常返回:
     *  - Controller
     *  - Method
     *  - Params
     *  - Middleware
     *
     * @return array
     * @throw \ErrorException
     */
    public function dispatch($uri) {
        $uri = parse_url($uri, PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];

        $placeholders = array_keys($this->placeholders);
        $replaces = array_values($this->placeholders);

        if (in_array($uri, $this->routes)) {
            $route_pos = array_keys($this->routes, $uri);

            foreach ($route_pos as $route) {
                if($result = $this->parseCallback($route, $method, [])) {
                    return $result;
                }
            }
        }else {
            foreach($this->routes as $k => $v) {
                if (strpos($v, ':') !== false) {
                    $route = str_replace($placeholders, $replaces, $v);

                    // 可选路由参数
                    if(strpos($route, '?') !== false) {
                        $route = str_replace('?', '|\s?', $route);
                    }
                }

                if (preg_match('#^' . $route . '$#', $uri, $matched) && ($result = $this->parseCallback($k, $method, $matched))) {
                    return $result;
                }
            }
        }

        throw new \ErrorException("Router $uri not found", 404);
    }

    /**
     * Parse Callback
     *
     * @param int    $index
     * @param String $method
     * @param array  $params
     * @return mixed
     */
    protected function parseCallback($index, $method, $params) {
        if((is_array($this->methods[$index]) && in_array($method, $this->methods[$index])) 
            || $this->methods[$index] == $method || $this->methods[$index] == 'ANY') {

            array_shift($params);
            $result = explode('@',$this->callbacks[$index]);
            array_push($result, $params, $this->stack[$index] ?? []);
            return $result;
        }

        return false;
    }
}
