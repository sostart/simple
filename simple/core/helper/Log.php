<?php
namespace core\helper;
/**
 * log help
 *
 *
 * @author LM <sostart.net@gmail.com>
 * @package system
 * @since 1.0
 */
class Log
{
	public static function __callStatic($name,$arguments)
	{
		return call_user_func_array(array(\Component::get('log'), $name), $arguments);
	}
}