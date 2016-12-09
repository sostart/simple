<?php
namespace core\helper;
/**
 * ActiveRecord 接口类
 *
 *
 * @author LM <sostart.net@gmail.com>
 * @package system
 * @since 1.0
 */
class AR
{
	public static function get($name)
	{
		$cachename = $name.'AR';
		
		if( $cache = \Singleton::has( $cachename ) )
		{
			return $cache;
		}

		return \Singleton::set( $cachename, new \ActiveRecord($name) );
	}
}
