<?php
namespace core\component;

class MysqlDB
{
	public $dsn;
	public $username;
	public $password;
	public $prefix;
	
	public function init()
	{
		list($drive,$conf) = explode(':',$this->dsn,2);
		foreach(explode(';',$conf) as $v)
		{
			list($k,$v) = explode('=',$v);
			$dsn[$k] = $v;
		}
		
		$this->link = mysql_connect($dsn['host'], $this->username, $this->password);
		mysql_select_db($dsn['dbname'], $this->link);
		mysql_query("SET NAMES 'UTF8'", $this->link); 
	}

	public function query($sql)
	{		
		if($rs = $this->execute($sql))
		{
			return new MysqlRS($rs);
		}
		else
		{
			return false;
		}
	}

	public function execute($sql)
	{
		$this->lastquery = $sql;
		return mysql_query($sql, $this->link);
	}

	public function get_insert_id()
	{
		return mysql_insert_id($this->link);
	}

	public function getLastSql()
	{
		return $this->lastquery;
	}
}

class MysqlRS
{
	public function __construct($rs)
	{
		$this->rs = $rs;
	}
	
	public function fetch()
	{
		return mysql_fetch_assoc($this->rs);
	}

	public function fetchAll()
	{
		$result=array();
		while($rs = mysql_fetch_assoc($this->rs)) $result[] = $rs;
		return $result;
	}
}