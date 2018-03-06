<?php namespace Rest;

use Rest\Http\Request;
use Rest\Http\Response;

/**
 * Rest
 * 
 * @package Rest
 * @author ky
 */
class Rest {

    /**
     * The application's config
     *
     * @var array
     */
    protected $config;

    /**
     * Construct
     *
     * @param String $path
     */
    public function __construct($config) {
        $this->config = require $config;
    }

    /**
     * Initialize
     *
     * @return void
     */
    public function initialize() {
        date_default_timezone_set($this->config['timezone']);

        $this->bindBaseServiceProvider();

        $this->bindCustomServiceProvider($this->config['providers']);

        Services::exceptions()
            ->initialize($this->config['outputCallbackException']);

        $this->bootstrapEnvironment();
    }

    /**
     * App Run
     *
     * @return void
     */
    public function run() {
        $body = $this->handle(Services::request());

        Services::response()
            ->write($body)
            ->send();
    }

    /**
     * Handle http request
     *
     * @param \Rest\Http\Request
     * @return mixed
     */
    public function handle($request) {
        list($controller, $method, $params, $middleware) = $this->dispatchToRouter($request);

        return (new Pipeline())
            ->send($request)
            ->through(array_merge($this->config['middleware'], $middleware))
            ->then(function($passable) use ($controller, $method, $params) {
                return Services::$controller()
                    ->$method(...$params);
            });
    }

    /**
     * Get the route dispatcher callback
     *
     * @param Rest\Http\Request $request
     * @return array
     */
    protected function dispatchToRouter($request) {
        $router = Services::router();
        require CONFIG_PATH . '/routes.php';

        return $router->dispatch($request->getPath());
    }

    /**
     * Bind Base Service Provider
     *
     * @return void
     */
    protected function bindBaseServiceProvider() {
        Services::bind('request', function() {
            return new Request;
        });

        Services::bind('response', function() {
            return new Response(200, ['Content-type: application/json;charset=utf-8'],
                Services::request()->params('format'));
        });

        Services::bind('router', function() {
            return new Router($this->config['placeholders'],
                $this->config['routeMiddleware'],
                $this->config['middlewareGroups']);
        });

        Services::bind('exceptions', function() {
            return new Exceptions(Services::response());
        });
    }

    /**
     * Bind Custom Service Provider
     *
     * @param array $providers
     * @return void
     */
    protected function bindCustomServiceProvider($providers) {
        foreach($providers as $name => $resolver) {
            Services::bind($name, $resolver);
        }
    }

    /**
     * BootStrap Environment
     *
     * @return void
     */
    protected function bootstrapEnvironment() {
        define('ENVIRONMENT', getenv('REST_ENV') !== false ? 'testing' 
            : $_SERVER['REST_ENV'] ?? 'production');

        switch(ENVIRONMENT) {
        case 'testing':
        case 'development':
            ini_set('display_errors', 1);
            error_reporting(-1);
            break;

        case 'production':
            ini_set('display_errors', 0);
            error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
            break;
        }
    }
}
