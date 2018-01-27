<?php namespace Rest;

/**
 * Rest api micro PHP 7 framework
 *
 * @package Rest
 * @version 1.0.0
 */

/**
 * Rest
 * 
 * @package Rest
 * @author ky
 */
class Rest {

    /**
     * app of config
     * @var array
     */
    private $app;

    /**
     * config path
     */
    private $configPath;

    /**
     * Construct
     *
     * @param String $path
     */
    public function __construct($path) {
        $this->configPath = $path;
        $this->app = require $this->configPath . '/App.php';
    }

    /**
     * Initialize
     *
     * @return void
     */
    public function initialize() {
        date_default_timezone_set($this->app['timezone']);

        $this->bindServiceProvider($this->app['providers']);

        Services::exceptions()
            ->initialize();

        $this->bootstrapEnvironment();
    }

    /**
     * App Run
     *
     * @return void
     */
    public function run() {
        $router = Services::router();
        $router->addPlaceholders($this->app['placeholders']);
        require $this->configPath . '/Routes.php';

        list($controller, $method, $params) = $router->dispatch();

        // Autowiring
        Services::$controller()
            ->$method(...$params);
    }

    /**
     * Bind Service Provider
     *
     * @param array $providers
     * @return void
     */
    protected function bindServiceProvider($providers) {
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
