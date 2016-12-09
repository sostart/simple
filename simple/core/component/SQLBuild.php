<?php
namespace core\component;

class SQLBuild
{
	public function init()
	{
	
	}

	public function select($select)
	{
		$this->op = 'select';
		$this->select = $select;
		return $this;
	}

	public function from($table)
	{
		$this->table($table);
		return $this;
	}

	public function update($table)
	{
		$this->op = 'update';
		$this->table($table);
		return $this;
	}

	public function delete($table)
	{
		$this->op = 'delete';
		$this->table($table);
		return $this;
	}

	public function table($table)
	{
		$this->table = $table;
		return $this;
	}
	
	// comparison operator c
	// logic operator l
	public function where($where)
	{
		if(is_array($where))
		{
			foreach($where as $k=>$v)
			{
				if(!is_array($v))
				{
					$tmp = $v;
					$v = array();
					$v['v'] = $tmp;
					$v['l'] = $sql?'AND':'';
					$v['c'] = '=';
				}

				$sql .= " {$v['l']} `{$k}` {$v['c']} '{$v['v']}'";
			}

			$where = $sql;
		}
		$this->where = $where;
		return $this;
	}

	public function limit($limit)
	{
		$this->limit = $limit;
		return $this;
	}

	public function set($set)
	{
		$this->set = $set;
		return $this;
	}

	public function getSql()
	{
		if($this->op=='select')
		{
			return "SELECT {$this->select} FROM `{$this->table}` WHERE {$this->where}";
		}
	}
}