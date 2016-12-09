<?php
namespace core\component;

class SessionComponent
{
	public function init()
	{
		if($_REQUEST['token'])
		{
			\Session::start($_REQUEST['token']);
		}
		else
		{
			\Session::start();
		}
	}

	public function start($id=false)
	{
		if($id) session_id($id);
		return session_start();
	}

	public function destroy()
	{
		session_unset();
		session_destroy();
		$_SESSION = array();
	}

	public function getID()
	{
		return session_id();
	}

	public function setID($id)
	{
		\Session::destroy();
		return \Session::start($id);
	}

	public function get($name)
	{
		$_tmp = $_SESSION;
		foreach(explode('.',$name) as $k)
		{
			$_tmp = $_tmp[$k];
		}
		return $_tmp;
	}

	public function Set($name,$value='')
	{
		if(is_array($name))
		{
			foreach($name as $k=>$v)
			{
				\Session::Set($k,$v);
			}
		}
		else
		{
			$_key = '';
			
			foreach(explode('.',$name) as $k)
			{
				$_key .= '['.$k.']';
			}
			eval("\$_SESSION$_key = \$value;");			
		}

		return true;
	}

	public function getAll()
	{
		return $_SESSION;
	}
}