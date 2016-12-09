<?php
namespace core\helper;
/**
 * Logic helper
 *
 *
 * @author LM <sostart.net@gmail.com>
 * @package system
 * @since 1.0
 */
class Logic
{
	public static function get($name)
	{
		return \Component::get('logic')->get($name);
	}

	public static function run($config=array())
	{
		if($config) \Config::set($config);
		
		return \Component::get('logic')->run(
			\Input::get(\Config::get('Component.logic.l')),
			\Input::get(\Config::get('Component.logic.m')),
			\Input::getAll()
		);
	}
}