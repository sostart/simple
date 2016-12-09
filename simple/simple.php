<?php
// 调试时开启,线上由php.ini控制
error_reporting(E_ALL ^ E_NOTICE);
date_default_timezone_set('Asia/ShangHai');

// 网站根目录
if(!defined('WEBROOT')) define('WEBROOT',realpath('.').DIRECTORY_SEPARATOR);
// 系统运行开始时间
if(!defined('BEGIN_TIME')) define('BEGIN_TIME',microtime(true));
// 系统根目录
if(!defined('SYSTEM_PATH')) define('SYSTEM_PATH',dirname(__FILE__).DIRECTORY_SEPARATOR);
// 应用目录
if(!defined('APPLICATION_PATH')) define('APPLICATION_PATH',SYSTEM_PATH);
// 项目基础
if(!defined('BASE_PATH')) define('BASE_PATH',APPLICATION_PATH.'base'.DIRECTORY_SEPARATOR);
// 项目扩展
if(!defined('APP_NAME')) define('APP_NAME','base');
// Core路径
define('CORE_PATH', SYSTEM_PATH.'core'.DIRECTORY_SEPARATOR);
// 平台路径
define('APP_PATH', APPLICATION_PATH.APP_NAME.DIRECTORY_SEPARATOR);

// 自动载入
spl_autoload_register(function($name){
	if(strpos($name,'\\')) // 有名字空间 并且不在根域
	{
		$name = str_replace(array('\\','/'),DIRECTORY_SEPARATOR,$name);
		foreach(array(APPLICATION_PATH,SYSTEM_PATH) as $path)
		{
			if(is_file($path.$name.'.php'))
			{
				require $path.$name.'.php'; break;
			}
		}
	}
	else
	{
		$name = str_replace(array('\\','/'),DIRECTORY_SEPARATOR,$name);
		foreach(array('helper','component','logic','controller','model','rule','api') as $path)
		{
			// 先在项目目录找
			$fileName = APP_PATH.$path.DIRECTORY_SEPARATOR.$name.'.php';
			if(is_file($fileName))
			{
				require $fileName;
				eval(($path=='api'?'interface':'class')." $name extends \\".APP_NAME."\\$path\\$name{}");
				break;
			}

			// 然后在base目录找
			$fileName = BASE_PATH.$path.DIRECTORY_SEPARATOR.$name.'.php';
			if(is_file($fileName))
			{
				require $fileName;
				eval(($path=='api'?'interface':'class')." $name extends \\base\\$path\\$name{}");
				break;
			}

			// 然后在系统目录找
			$fileName = CORE_PATH.$path.DIRECTORY_SEPARATOR.$name.'.php';
			if(is_file($fileName))
			{
				require $fileName;
				eval(($path=='api'?'interface':'class')." $name extends \\core\\$path\\$name{}");
				break;
			}
		}
	}
});

// 初始化配置
Config::init();


// class Cooly{}