<?php
namespace core\component;

class SaeKVDBComponent
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
		return static::$obj->get($name);
	}

	public static function set($name,$value,$expire=3600)
	{
		return static::$obj->set($name, $value);
	}

	public static function delete($name)
	{
		return static::$obj->delete($name);
	}
}