<?php
namespace core\helper;
/**
 * 存储接口
 *
 *
 * @author LM <sostart.net@gmail.com>
 * @package system
 * @since 1.0
 */
class Storage
{
	public static function __callStatic($name,$arguments)
	{
		return call_user_func_array(array(\Component::get('storage'), $name), $arguments);
	}
}