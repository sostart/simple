<?php
namespace core\helper;
/**
 * SQL数据库接口类
 *
 *
 * @author LM <sostart.net@gmail.com>
 * @package system
 * @since 1.0
 */
class DB
{
	public static function __callStatic($name,$arguments)
	{
		return call_user_func_array(array(\Component::get('db'), $name), $arguments);
	}
	/*
	public static function query($sql,$bind=array())
	{
		return \Component::get('db')->query($sql,$bind);
	}

	public static function execute($sql,$bind=array())
	{
		return \Component::get('db')->execute($sql,$bind);
	}

	public static function getLastSql()
	{
		return \Component::get('db')->getLastSql();
	}
	*/
}