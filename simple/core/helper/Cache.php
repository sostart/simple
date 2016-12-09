<?php
namespace core\helper;
/**
 * 缓存接口类
 *
 *
 * @author LM <sostart.net@gmail.com>
 * @package system
 * @since 1.0
 */
class Cache
{
	public static function get($name,$default=NULL)
	{
		return \Component::get('cache')->get($name,$default);
	}

	public static function set($name,$value,$expire=3600)
	{
		if($value===NULL)
		{
			return \Component::get('cache')->delete($name);
		}
		else
		{
			return \Component::get('cache')->set($name,$value,$expire);
		}
	}

	public static function delete($name)
	{
		return \Component::get('cache')->delete($name);
	}
}