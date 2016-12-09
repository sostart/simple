<?php
namespace core\component;

class HttpInput
{
	public static function init()
	{
	
	}

	public static function set($name,$value)
	{
		$_REQUEST[$name] = $value;
		return true;
	}

	public static function get($name,$default=NULL)
	{
		return isset($_REQUEST[$name])?$_REQUEST[$name]:$default;
	}

	public static function getAll()
	{
		return $_REQUEST;
	}
}