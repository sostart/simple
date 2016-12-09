<?php
namespace core\helper;
/**
 * 视图
 *
 *
 * @author LM <sostart.net@gmail.com>
 * @package system
 * @since 1.0
 */
class View
{
	public static function __callStatic($name,$arguments)
	{
		return call_user_func_array(array(\Component::get('view'), $name), $arguments);
	}
}