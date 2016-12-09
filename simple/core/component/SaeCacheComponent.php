<?php
namespace core\component;

class SaeCacheComponent
{
	public static $obj;
	public static $compressed = false;

	public function init()
	{
		if( !(static::$obj = \Singleton::has('SaeKV')) )
		{
			static::$obj = new \SaeKV();
			static::$obj->init();
			\Singleton::set('SaeKV',static::$obj);
		}
	}

	public static function get($name,$default=NULL)
	{
		$name = 'saecache_'.$name;
		return static::$obj->get($name);
	}

	public static function set($name,$value,$expire=3600)
	{
		$name = 'saecache_'.$name;
		return static::$obj->set($name, $value);
	}

	public static function delete($name)
	{
		$name = 'saecache_'.$name;
		return static::$obj->delete($name);
	}
}