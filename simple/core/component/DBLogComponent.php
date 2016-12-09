<?php
namespace core\component;

class DBLogComponent
{
	public $table='SystemLog';

	public function init()
	{
		
	}

	public function info($msg,$type=0)
	{
        return $this->write($msg,$type);
	}

    public function write($msg,$type=0,$ip=false)
    {
        if( !is_numeric($ip) ||  $ip==false )
        {
            $ip = \Common::client_ip();
        }

		if(is_array($msg))
		{
			$msg = \Common::JsonEncode($msg);
		}

        return \AR::Get($this->table)->add(array(
            'type'=>$type,
            'content'=>$msg,
            'client_ip'=>$ip,

            'create_time'=>time(), 
        ));
    }
}