<?php
$_domain = 'http://'.$_SERVER['HTTP_HOST'].(($_SERVER['SERVER_PORT']==80)?'':':'.$_SERVER['SERVER_PORT']).'/';
return  array(	
	'systemPath'=>SYSTEM_PATH,
	
	'domain'=>$_domain,
	'resourceServer'=>$_domain.'client/',

	'mode'=>0, // 1 严谨模式, 必须定义 API抽象类 和  API Rule

	// 通信密钥
	'secret'=>'1122334',
	
	'Component'=>array(
		
		'view'=>array(
			'class'=>'ViewComponent',
		),

		'controller'=>array(
			'class'=>'ControllerComponent',
			'm'=>'m', // 模块
			'c'=>'c', // 控制器
			'a'=>'a',  // 动作
			
			'_m'=>'', // 默认模块
			'_c'=>'Home',  // 默认控制器
			'_a'=>'index', // 默认动作
		),

		/**
		 * SessionComponent  原生SESSION
		 */
		'session'=>array(
			'class'=>'SessionComponent',
		),
		
		'api'=>array(
			'class'=>'OpenApi',

			'appid'=>1111111111,
			'appkey'=>'1111111111111111',
			//'server_name'=>'1.254.254.22', // cee 测试使用此IP
			//'server_name'=>'119.147.19.43', // 其它 测试使用此IP
			'server_name'=>'openapi.tencentyun.com', // 正式部署使用此域名

			'pf'=>'qzone'
		),

		'log'=>array(
			'class'=>'FileLogComponent',
			'file'=>'log.txt',
		),
		
		'logic'=>array(
			'class'=>'LogicComponent',
			'l' =>'l',
			'm'=>'m',
		),
		
		// 数据模型组件
		'model'=>array(
			'class'=>'ModelComponent',
		),
		
		// 输入组件
		'input'=>array(
			'class'=>'HttpInput',	
		),
		
		// 响应组件
		'response'=>array(
			'class'=>'JsonResponse'
		),
		
		// 数据库组件
		'db'=>array(
			'class'=>'MysqlDB', //'PdoDB',
			'dsn'=>'mysql:host=127.0.0.1;dbname=jeweled;charset=utf8',
			'username'=>'root',
			'password'=>'',
			'prefix'=>''
		),
		
		// SQL组装组件
		'sqlbuild'=>array(
			'class'=>'SQLBuild',
		),
		
		// 缓存组件
		'cache'=>array(
			'class'=>'MemcacheComponent',
			'host'=>'127.0.0.1',
			'port'=>'11211',
		),
		
		// 文件存储组件
		'storage'=>array(
			'class'=>'BaseStorageComponent',
			'domain'=>WEBROOT.'resource/', // 资源存放目录
			'domain_url'=>'http://resource.sostart.net/resource/', // 资源存放目录的url地址
		),
	),
);