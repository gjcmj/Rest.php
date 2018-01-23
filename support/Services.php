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
 * @package Rest
 * @author ky
 */
class Services {

    protected static $_registry = [];

    protected static $_instances = [];

    public static function bind($name, $resolver) {
        static::$_registry[$name] = $resolver;
    }

    public static function __callStatic($name, $params) {
        $share = $params[0] ?? true;

        if($share && isset(static::$_instances[$name])) {
            return static::$_instances[$name];
        }

        return self::make($name, $share);
    }

    protected static function make($name, $share = true) {
        if(empty($className = static::$_registry[$name])) {
            throw new \Exception('Alias does not exist in the Services bind.');
        }

        if($className instanceof \Closure) {
            return $share ? static::$_instances[$name] = $className() : $className();
        }

        $reflector = new \ReflectionClass($className);

        // 检查类是否可实例化, 排除抽象类abstract和对象接口interface
        if (!$reflector->isInstantiable()) {
            throw new \Exception("Can't instantiate this.");
        }

        /** @var ReflectionMethod $constructor 获取类的构造函数 */
        $constructor = $reflector->getConstructor();

        // 若无构造函数，直接实例化并返回
        if (is_null($constructor)) {
            return $share ? static::$_instances[$name] = new $className : new $className;
        }

        // 取构造函数参数,通过 ReflectionParameter 数组返回参数列表
        $parameters = $constructor->getParameters();

        // 递归解析构造函数的参数
        $dependencies = self::getDependencies($parameters, $share);

        // 创建一个类的新实例，给出的参数将传递到类的构造函数。
        return $share ? static::$_instances[$name] = $reflector->newInstanceArgs($dependencies) :
            $reflector->newInstanceArgs($dependencies);
    }

    protected static function getDependencies($parameters, $share) {
        $dependencies = [];

        foreach ($parameters as $parameter) {
            /** @var ReflectionClass $dependency */
            $dependency = $parameter->getClass();

            if (is_null($dependency)) {
                // 是变量,有默认值则设置默认值
                !$parameter->isDefaultValueAvailable() ?? $dependencies[] = $parameter->getDefaultValue();
            } else {
                // 是一个类，递归解析
                $dependencies[] = $self::make($dependency->name, $share);
            }
        }

        return $dependencies;
    }
}
