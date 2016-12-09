<?php
namespace core\helper;
/**
 * 数据接收接口类
 *
 *
 * @author LM <sostart.net@gmail.com>
 * @package system
 * @since 1.0
 */
class Input
{
	public static function get($name,$default=NULL)
	{
		return \Component::get('input')->get($name,$default);
	}

	public static function getAll()
	{
		return \Component::get('input')->getAll();
	}
}