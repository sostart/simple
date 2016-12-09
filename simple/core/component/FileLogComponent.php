<?php
namespace core\component;

class FileLogComponent
{
	public $file;

	public function init()
	{
		
	}

	public function info($msg,$type=0)
	{

		if(is_array($msg))
		{
			$msg = \Common::JsonEncode($msg);
		}

		return file_put_contents($this->file, $type.' '.date('Y-m-d H:i:s').' '.$msg.PHP_EOL, FILE_APPEND);
	}
}