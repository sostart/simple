<?php
namespace core\helper;
/**
 * 配置类
 *
 * 所有配置项设置及获取
 *
 * @author LM <sostart.net@gmail.com>
 * @package system
 * @since 1.0
 */
class Config
{
	public static $_conf;
	
	public static function init()
	{
		\Config::$_conf = \Common::array_merge_recursive(require CORE_PATH.'config.php', require BASE_PATH.'config.php', require APP_PATH.'config.php');
	}
	
	public static function getAll()
	{
		return \Config::$_conf;
	}
	
	public static function get($name)
	{
		$_tmp = \Config::getAll();
		foreach(explode('.',$name) as $k)
		{
			$_tmp = $_tmp[$k];
		}
		return $_tmp;
	}
	
	public static function set($name,$value=NULL)
	{
		if(is_array($name))
		{
			foreach($name as $k=>$v)
			{
				\Config::set($k,$v);
			}
		}
		else
		{
			$_tmp =& \Config::$_conf;
			foreach(explode('.',$name) as $k)
			{
				$_tmp =& $_tmp[$k];
			}
			return $_tmp = $value;		
		}
	}
}
