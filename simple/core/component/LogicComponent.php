<?php
namespace core\component;
/**
 * Logic组件
 *
 * @author LM <sostart.net@gmail.com>
 * @package system
 * @since 1.0
 */
class LogicComponent
{
	public function init()
	{

	}

	public function get($name)
	{
		return \Singleton::get($this->getName($name));
	}

	public function getName($name)
	{
		return "{$name}Logic";
	}

	public function run($name,$method,$param=array())
	{
		if(!is_array($param)) $param=array();

		// 接口规则校验
		\APIRule::Check($name,$method,$param);
		
		// 初始化方法是否存在,存在则调用
		if(is_callable(array(\Logic::get($name),'init')))
		{
			call_user_func_array( array(\Logic::get($name), 'init'), array(&$param) );
		}
		
		// 调用接口
		return call_user_func_array( array(\Logic::get($name), $method), array(&$param) );	
	}
}