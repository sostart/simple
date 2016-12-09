<?php
namespace core\helper;

class Controller
{
	public static function __callStatic($name,$arguments)
	{
		return call_user_func_array(array(\Component::get('controller'), $name), $arguments);
	}
}