<?php
namespace core\helper;
/**
 * 装载器
 *
 *
 * @author LM <sostart.net@gmail.com>
 * @package system
 * @since 1.0
 */
class Loader
{
	public static $loaded = array();

	public static function Lib($name)
	{
		return \Loader::Libraries($name);
	}

	public static function Libraries($name)
	{
		exit('Loader helper ..');
	}

	public static function Third($name)
	{
		if(in_array($name,static::$loaded)) return true;

		foreach(array(APP_PATH,BASE_PATH,CORE_PATH) as $path)
		{
			$fileName = $path.'third'.DIRECTORY_SEPARATOR.$name.'.php';	
			if(is_file($fileName))
			{
				static::$loaded[] = $name;
				require $fileName; return true;
			}
		}
		exit('third 未找到文件'.$name);
	}
}