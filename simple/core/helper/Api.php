<?php
namespace core\helper;

class Api
{
	public static function __callStatic($name,$arguments)
	{
		return call_user_func_array(array(\Component::get('api'), $name), $arguments);
	}
}