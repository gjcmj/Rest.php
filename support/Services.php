<?php namespace Rest;

/**
 * Rest api micro PHP 7 framework
 *
 * @package Rest
 * @version 1.0.0
 */

/**
 * Services
 *
 * Dependency injection container
 * 支持延迟载入、自动绑定、是否单例实例
 * 
 * @package Rest
 * @author ky
 */
class Services {

    /**
     * 绑定的注册服务
     *
     * @var array
     */
    protected static $_registry = [];

    /**
     * 共享的实例(singleton)
     *
     * @var array
     */
    protected static $_instances = [];

    /**
     * Bind 
     *
     * @param String $name
     * @param Mix $resolver
     * @return void
     */
    public static function bind($name, $resolver) {
        static::$_registry[$name] = $resolver;
    }

    /**
     * Get Instance
     *
     * @param String $name
     * @param array $params
     * @return mixed
     */
    public static function __callStatic($name, $params) {
        $share = $params[0] ?? true;

        if($share && isset(static::$_instances[$name])) {
            return static::$_instances[$name];
        }

        return self::make($name, $share);
    }

    /**
     * Make Instance
     *
     * @param String $name
     * @param bool $share
     * @return array
     */
    protected static function make($name, $share = true) {
        $className = static::$_registry[$name] ?? $name;

        if($className instanceof \Closure) {
            return $share ? static::$_instances[$name] = $className() : $className();
        }

        $reflector = new \ReflectionClass($className);

        if (!$reflector->isInstantiable()) {
            throw new \ReflectionException("Can't instantiate $className .");
        }

        $constructor = $reflector->getConstructor();

        if (is_null($constructor)) {
            return $share ? static::$_instances[$name] = new $className : new $className;
        }

        $parameters = $constructor->getParameters();

        $dependencies = self::getDependencies($parameters, $share);

        return $share ? static::$_instances[$name] = $reflector->newInstanceArgs($dependencies) :
            $reflector->newInstanceArgs($dependencies);
    }

    /**
     * Get Dependencies
     *
     * @param array $parameters
     * @param bool $share
     * @return array
     */
    protected static function getDependencies($parameters, $share) {
        $dependencies = [];

        foreach ($parameters as $parameter) {
            $dependency = $parameter->getClass();

            if (is_null($dependency)) {
                !$parameter->isDefaultValueAvailable() ?? $dependencies[] = $parameter->getDefaultValue();
            } else {
                $dependencies[] = self::make($dependency->name, $share);
            }
        }

        return $dependencies;
    }
}
