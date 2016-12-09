<?php
namespace core\component;

class ControllerComponent
{
	public function init()
	{
		
	}

	public function get($name,$module=false)
	{
		return \Singleton::get($this->getName($name,$module));
	}

	public function getName($name,$module=false)
	{
		$module = $module ? $module."\\" : '' ;
		return str_replace(array('\\','/'),"\\","\\".APP_NAME."\\controller\\{$module}{$name}Controller");
	}
	
	public function getModule()
	{
		return $this->module;
	}

	public function getController()
	{
		return $this->controller;
	}

	public function getAction()
	{
		return $this->action;
	}

	public function run($config=array())
	{
		if($config) \Config::set($config);
		
		$param = $_REQUEST;

		$param = \Security::xss_clean($param);
		
		$m = \Config::get('Component.controller.m');
		$c = \Config::get('Component.controller.c');
		$a = \Config::get('Component.controller.a');
		
		$this->module = $m ? $param[$m] : false;
		$this->controller = $param[$c] ? $param[$c] : \Config::get('Component.controller._c');
		$this->action = $param[$a] ? $param[$a] : \Config::get('Component.controller._a');
		
		// 检测 控制器/动作 是否可用
		if(!method_exists(\Controller::getName($this->controller,$this->module),$this->action))
		{
			\Common::show_404('未知的控制器/动作');
		}

		call_user_func_array( array(\Controller::get($this->controller,$this->module), $this->action), array(&$param) );	
	}
}