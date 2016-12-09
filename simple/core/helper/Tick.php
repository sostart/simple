<?php
namespace core\helper;
/**
 * 计时 help
 *
 *
 * @author LM <sostart.net@gmail.com>
 * @package system
 * @since 1.0
 */
class Tick
{
	public static $_ticks=array();
	
	public static function init()
	{
	
	}

	public static function start($flag,$time=false)
	{
		return \Tick::$_ticks[$flag]['start'] = $time?$time:microtime(true);
	}

	public static function end($flag)
	{
		\Tick::$_ticks[$flag]['end'] = microtime(true);
		return \Tick::$_ticks[$flag]['times'] = \Tick::$_ticks[$flag]['end'] - \Tick::$_ticks[$flag]['start'];
	}

	public static function endAll()
	{
		foreach(\Tick::$_ticks as $name=>$v)
		{
			$v['end'] || \Tick::end($name);
		}
		return static::getAll();
	}

	public static function get($flag='')
	{
		if(\Tick::$_ticks[$flag] && !isset(\Tick::$_ticks[$flag]['times'])) \Tick::end($flag);
		return $flag ? \Tick::$_ticks[$flag] : \Tick::getAll();
	}

	public static function getAll()
	{
		return \Tick::$_ticks;
	}
}