<?php
namespace core\helper;
/**
 * 单例
 *
 * @author LM <sostart.net@gmail.com>
 * @package system
 * @since 1.0
 */
class Singleton
{
	public static $instance;
	
	/**
	* 获取类的唯一实例
	*
	* 如果不存在实例则自动实例化类并缓存
	*
	* @param string $className 类名(带名字空间)
	* @return object 返回类实例
	*/
	public static function get($className)
	{
		if(isset(\Singleton::$instance[$className]))
		{
			return \Singleton::$instance[$className];
		}
		else
		{
			return \Singleton::set($className,new $className);
		}
	}

	/**
	* 缓存类实例
	*
	* @param string $className 类名(带名字空间)
	* @param object $instance 类实例
	* @return object 返回类实例
	*/
	public static function set($className,$instance)
	{
		return \Singleton::$instance[$className] = $instance;
	}

	/**
	* 查询类实例是否存在
	*
	* @param string $className 类名(带名字空间)
	* @return object|null 返回类实例或null
	*/
	public static function has($className)
	{
		return \Singleton::$instance[$className];
	}
}