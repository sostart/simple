<?php
namespace core\helper;
/**
 * 组件管理类
 *
 * 组件实例化,获取,设置
 *
 * @author LM <sostart.net@gmail.com>
 * @package system
 * @since 1.0
 */
class Component
{
	public static $instance;

	public function init()
	{
		
	}

	public static function get($id,$conf=array())
	{
		if(isset(\Component::$instance[$id]))
		{
			return \Component::$instance[$id];
		}
		else
		{
			$conf = array_merge(\Config::get("Component.{$id}"),$conf);
			\Component::set($id,new $conf['class']);
			$Component = \Component::get($id);
			foreach($conf as $k=>$v) $Component->$k = $v;
			$Component->init();
			return  $Component;
		}
	}

	public static function set($id,$instance)
	{
		return \Component::$instance[$id] = $instance;
	}
}