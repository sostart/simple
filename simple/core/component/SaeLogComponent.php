<?php
namespace core\component;

class SaeLogComponent
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

		sae_set_display_errors(false);
		$rs = sae_debug($msg);
		sae_set_display_errors(true);
		
		return $rs;
	}
}