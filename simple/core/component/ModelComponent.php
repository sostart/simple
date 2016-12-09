<?php
namespace core\component;

class ModelComponent
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
		return "{$name}Model";
	}
}