<?php namespace Rest;

/**
 * Rest api micro PHP 7 framework
 *
 * @package Rest
 * @version 1.0.0
 */

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


    public function __construct() {}

    /**
     * Request Method:
     * - GET
     * - POST
     * - PUT
     * - DELETE
     * - OPTIONS
     *
     * @return void
     */
    public function __call($method, $params) {
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
     *
     * @return array
     * @throw \ErrorException
     */
    public function dispatch() {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];

        $placeholders = array_keys($this->placeholders);
        $replaces = array_values($this->placeholders);

        if (in_array($uri, $this->routes)) {
            $route_pos = array_keys($this->routes, $uri);

            foreach ($route_pos as $route) {
                if((is_array($this->methods[$route]) && in_array($method, $this->methods[$route])) 
                    || $this->methods[$route] == $method || $this->methods[$route] == 'ANY') {

                    $result = explode('@',$this->callbacks[$route]);
                    array_push($result, []);
                    return $result;
                }
            }
        }else {
            foreach($this->routes as $k => $v) {
                if (strpos($v, ':') !== false) {
                    $route = str_replace($placeholders, $replaces, $v);
                    if(strpos($route, '?') !== false) {
                        $route = str_replace('?', '|\s?', $route);
                    }
                }

                if (preg_match('#^' . $route . '$#', $uri, $matched)) {
                    if((is_array($this->methods[$k]) && in_array($method, $this->methods[$k])) 
                        || $this->methods[$k] == $method || $this->methods[$k] == 'ANY') {

                        array_shift($matched);
                        $result = explode('@', $this->callbacks[$k]);
                        array_push($result, $matched);
                        return $result;
                    }
                }
            }
        }

        throw new \ErrorException("Router [$uri] not found", 404);
    }
}
