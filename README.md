# Rest api micro PHP 7 framework

轻量级接口框架, 基于 [Composer](http://www.phpcomposer.com/)

## Overview

- 针对 API 接口，轻巧、快速
- 支持 *RESTful* 
- 使用 *HTTP Status Code* 表示状态
- 核心服务全部基于依赖注入, 包括控制器等
- 支持HTTP 中间件,便于过滤等操作
- 方便任意扩展
- 简单定义错误信息
- 未封装ORM

## Quick start

### Install
	
	composer create-project gjcmj/rest.php path 1.0.0

### Config

所有配置文件均放在app/Config 目录下. 可通过support/bootstrap.php 中 CONFIG_PATH 指定路径.

环境配置:

CLI

	REST_ENV=testing php -S 127.0.0.1:2222
	
Nginx

	fastcgi_param  REST_ENV	 testing;


	
### Directory

    |-- app                 working
    |     |-- Config
    |     |-- Controllers
    |     |-- Models
    |
    |-- public              发布目录
    |-- support             核心支持类库
          |-- Helper        助手类库
          |-- Http          Http 库


### Run

CLI (以 testing 运行) :

	cd public && REST_ENV=testing php -S 127.0.0.1:2222
	
Nginx:
    
    location / {
    	try_files $uri $uri/ /index.php?$query_string;
	}

## The Basics

### Router

GET路由

	$router->get('/', 'App\Demo\DemoController@index');
	
其它路由

	$router->post('/', 'App\Demo\DemoController@index');
	
	$router->put('/', 'App\Demo\DemoController@index');
	
	$router->delete('/', 'App\Demo\DemoController@index');
	
为多种请求注册路由

	$router->match(['get', 'post'], '/', 'App\Demo\DemoController@index');
	
为所有请求注册路由

	$router->any('/', 'App\Demo\DemoController@index');
	
路由参数 (placeholder)

默认

	':any' => '[^/]+',
	':num' => '[0-9]+',
	':all' => '.*'

使用

	$router->get('/(:num)', 'App\Demo\DemoController@index');

自定义路由参数 (app/Config/app.php)

	'placeholders' => [
        ':id'  => '[0-9]+'
    ]
    
可选路由参数

	$router->get('/(:id?)', 'App\Demo\DemoController@index');
	
取得路由参数值 (App/Demo/DemoController.php)

	class DemoController extends Controller {
	
		public function index($id, $name) {
            return [
                'id'   => $id,
                'name' => $name
            ];
    	}
	}

### Middleware

中间件提供一个便利的方式来过滤HTTP 请求, 例如, 验证请求合法化等

    <?php namespace App\Middleware;

    use Closure;
    use Rest\Http\Request;
    use App\Config\Error;
    
    class Before {
    
        public function handle(Request $request, Closure $next) {
    
	        $this->request->requireParams(['uid' , 'vid']) || throw_exception(Error::INVALID_PARAMETER);
    
            return $next($request);
        }
    }

请求后执行一些后置操作

    <?php namespace App\Middleware;

    use Closure;
    use Rest\Http\Request;
    
    class After {
    
        public function handle(Request $request, Closure $next) {
    
            $response = $next($request);

                ...

            $response->setHeader($header);
    
            return $response;
        }
    }

注册中间件

    中间件分三类，分别是全局中间件, 自定义中间件, 中间件组

全局中间件(app/Config/app.php)

每个HTTP 请求都会执行

    'middleware' => [
        \App\Middleware\Test::class
    ]

自定义中间件(指定)

    'routeMiddleware' => [
        'before' => \App\Middleware\Before::class,
        'after' => \App\Middleware\After::class,
        'test' => \App\Middleware\Test::class
    ]

定义后路由指派

    $router->get('/(:id)/name/(:all?)', 'App\Demo\DemoController@index')->middleware('before', 'after');

中间组

    'middlewareGroups' => [
        'auth' => [
            \App\Middleware\Test::class,
            \App\Middleware\Before::class,
            \App\Middleware\After::class
        ]
    ]

定义后路由指派

    $router->group(['auth'], function($router) {

        $router->get('/test', 'App\Demo\DemoController@group');

    });

### Controller

所有控制器均应继承基类 Rest\Controller

基础控制器

	<?php namespace App\Demo;
	
	use Rest\Controller;
	use App\Config\Errors;
	
	class DemoController extends Controller {
	
		public function index($id, $name) {
			
			$name == 'test' || throw_exception(Errors::BAD_REQUEST);
			return $id;
    	}
	}
	
依赖注入和控制器

	<?php namespace App\Demo;
	
	use Rest\Controller;
	use App\Config\Errors;
	
	class DemoController extends Controller {
		
		private $model;
		
		/**
     	 * 自动注入 App\Demo\DemoModel 实例, 无需显示 new 对象
         */
    	public function __construct(DemoModel $model) {
       	$this->model = $model;
    	}
	
		public function index($id, $name) {
        	return $this->model->test($id, $name);
   		}
	}

### Request

取得参数

	<?php namespace App\Demo;
	
	use Rest\Controller;
	
	class DemoController extends Controller {
		
		public function index($id) {
			
			/**
			 * GET、POST、PUT、DELETE 等均用以下方式
			 * $key 为参数名，$default 为默认值，如果$key 不存在或为空时
			 */
			return $this->request->params($key, $default);
		}
	}
	
必填参数
	
uid, vid 必填, 否则抛出异常由统一异常处理

	$this->request->requireParams(['uid' , 'vid']) || throw_exception(Error::INVALID_PARAMETER);
	
参数过滤

uid 必填, 以 decode_id 回调过滤, 此回调函数可在助手函数中自定义

	$this->request->requireParams(['uid' => 'decode_id']) || throw_exception(Error::INVALID_PARAMETER);

获取请求方法

    $this->request->getMethod();

获取请求 IP

    $this->request->getIp();

获取 UserAgent

    $this->request->getUserAgent();

获取 client type

    $this->request->getClientType();

获取 clinet version

    $this->request->getClientVersion();
    
### Response

基本响应

	<?php namespace App\Demo;
	
	use Rest\Controller;
	use App\Config\Errors;
	
	class DemoController extends Controller {
	
		public function index($id, $name) {
        	
            return ['id' => $id, 'name' => $name];
   		}
	}
	
格式化 json

	url 后追加参数 format=1 即可

缓存
	 
	 // last-modify 方式，在 after 中间件默认 10 分钟
    $response = $next($request);
    $response->cache();
    
自定义输出头

    // 相同的头, 默认后面会替换之前的
    // after 中间件
    $response = $next($request);
    $response->setHeader($header, $replace = true);
    
Set Json encode options

 [http://php.net/manual/en/json.constants.php](http://php.net/manual/en/json.constants.php)

	$response->setJsonEncodeOptions($options)

### Model

所有控制器均应继承基类 Rest\Model

	<?php namespace App\Demo;

	use Rest\Model;

	class DemoModel extends Model {
	
		private $db;
		
		public function __construct() {
		
			// 连接 mysql 中 user 库
			$db = $this->connection('user', 'mysql');
		}
		...
	}
	
数据库相关配置参看 app/Config/Database/ 下配置文件
	
### Services

所有核心及控制器等, 均基于依赖注入容器 Services

显式 Bind 服务

	Services::bind('request', function() {
		return new \Rest\Http\Request;
	});
	
	Services::bind('response', 'Rest\Http\Response');
	
通过配置 Bind 自定义服务 ( app/Config/app.php )

	'providers' => [
        'test' => function() {
            return new \App\test;
        }
    ]

自动绑定服务

DemoController, DemoModel 均自动绑定服务并注入相关服务

    <?php namespace App\Demo;

    use App\Config\Errors;
    use Rest\Controller;

    class DemoController extends Controller {

        private $model;

        /**
         * 自动注入(仅在构造器部分)  App\Demo\DemoModel 实例
         */
        public function __construct(DemoModel $model) {
            $this->model = $model;
        }
        ...
    }
    
获取服务类实例

	// singleton 模式
	Services::request();
	
	// 每次均为新实例
	Services::request(false);
	
通过助手函数获取

	service('request');
	service('response', false);

### Exceptions

异常由框架统一处理, 可定制返回格式

自定义逻辑错误信息 (app/Config/Errors.php)

	<?php namespace App\Config;

	class Errors {

    	/**
     	 * - 500 (HTTP Status Code)
     	 * - 1   (1 系统级错误 / 2 服务级错误)
     	 * - 01  (00 lua / xx 模块)
     	 * - 00  (具体错误)
     	 */
    	const INTERNAL_ERROR = '500 10100 Internal system error';

    	const BAD_REQUEST    = '404 20100 Bad request';

    	const MISS_PARAMETER = '400 20101 Miss required parameter';
	}

自定义错误信息回调函数 (app/Config/app.php)

	'outputCallbackException' => function($message) {
        $isCustom = is_numeric(substr($message, 0, 3));
	
	    // 错误输出格式, -1 为系统默认错误统一代码
        return [
            'code' => $isCustom ? substr($message, 4, 5) : -1,
            'message' => $isCustom ? substr($message, 10) : $message
        ];
    }
    
通过助手函数抛出异常

	<?php namespace App\Demo;
	
	use Rest\Controller;
	use App\Config\Errors;
	
	class DemoController extends Controller {
	
		public function index($id, $name) {
			
			$name == 'test' || throw_exception(Errors::BAD_REQUEST);
			return $id;
    	}
	}

### Helpers

可通过 composer.json files 定义每次均加载的助手库


## Security

XSS 过滤

    $this->request->requireParams(['content' => 'xss_clean']) or throw_exception(Error::INVALID_PARAMETER);
    $content = $this->request->params('content');

or

    $content = xss_clean($this->request->params('content'));
    
TODO CSRF
