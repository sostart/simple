<?php
namespace core\component;

class JsonResponse
{
	public function init()
	{
		
	}

	public static function send($data,$exit=true)
	{
		if(\Config::get('debug'))
		{
			echo preg_replace_callback('/\\\u(\w\w\w\w)/', function($matches){
				return mb_convert_encoding('&#'.hexdec($matches[1]).';','UTF-8','HTML-ENTITIES');
				//return '&#'.hexdec($matches[1]).';';
			}, json_encode($data));
		}
		else
		{
			echo json_encode($data);
		}

		$exit && exit;
	}
}