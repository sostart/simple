<?php
namespace core\component;

class PdoDB
{
	public $dsn;
	public $username;
	public $password;
	public $prefix;

	private $_rs;
	
	public function init()
	{
		$this->db = new \PDO($this->dsn,$this->username,$this->password);
	}

	public function query($sql,$bind=array())
	{
		if($this->execute($sql,$bind))
		{
			$this->_rs->setFetchMode(\PDO::FETCH_ASSOC);
			return new PdoRS($this->_rs);
		}
		else
		{
			return false;
		}
	}

	public function execute($sql,$bind=array())
	{
		// \Log::info($sql);

		$this->_rs = $this->db->prepare($sql,array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
		$this->lastquery = $rs->queryString;
		return $this->_rs->execute($bind);
	}

	public function getLastSql()
	{
		return $this->lastquery;
	}
}

class PdoRS
{
	public function __construct($rs)
	{
		$this->rs = $rs;
	}
	
	public function fetch()
	{
		return $this->rs->fetch();
	}

	public function fetchAll()
	{
		return $this->rs->fetchAll();
	}
}