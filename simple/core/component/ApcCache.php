<?php
namespace core\component;
/**
 * »ùÓÚAPCµÄ»º´æ
 *
 * @author LM <sostart.net@gmail.com>
 * @package system
 * @since 1.0
 */
class ApcCache
{
	public function init()
	{
		
	}

	public static function get($name,$default=NULL)
	{
		return apc_exists($name) ? apc_fetch($name) : $default;
	}

	public static function set($name,$value,$expire=3600)
	{
		return apc_store($name,$value,$expire);
	}

	public static function delete($name)
	{
		return apc_delete($name);
	}
}