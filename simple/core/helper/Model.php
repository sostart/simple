<?php
namespace core\helper;
/**
 * Model接口类
 *
 *
 * @author LM <sostart.net@gmail.com>
 * @package system
 * @since 1.0
 */
class Model
{
	public static function get($name)
	{
		return \Component::get('model')->get($name);
	}
}