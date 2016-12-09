<?php
namespace core\helper;
/**
 * SESSION接口类
 *
 *
 * @author LM <sostart.net@gmail.com>
 * @package system
 * @since 1.0
 */
class Session
{
	public static function __callStatic($name,$arguments)
	{
		return call_user_func_array(array(\Component::get('session'), $name), $arguments);
	}
}