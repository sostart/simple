<?php
namespace core\component;

class MemcacheComponent
{
	public static $memobj;
	public static $compressed = false;

	public function init()
	{
		static::$memobj = memcache_connect($this->host, $this->port);
	}

	public static function get($name,$default=NULL)
	{
		return memcache_get( static::$memobj, $name );
	}

	public static function set($name,$value,$expire=3600)
	{
		return memcache_set( static::$memobj, $name, $value, static::$compressed, $expire);
	}

	public static function delete($name)
	{
		return memcache_delete( static::$memobj, $name );
	}
}