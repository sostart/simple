<?php
namespace core\helper;

class Builder
{
	/**
	* 存储条件
	*/
	public $_builder;

	/**
	* 清空条件
	*/
	public function fresh()
	{
		$this->_builder = array();
	}
	
	/**
	* 添加
	*/
	public static function insert($table,$data=NULL)
	{
		$instance = new static;
		
		$instance->_builder['type'] = 'insert';
		$instance->table($table);
		$instance->data($data);
		
		return $instance;
	}

	/**
	* 删除
	*/
	public static function delete($table,$where=NULL)
	{
		$instance = new static;
		
		$instance->_builder['type'] = 'delete';
		$instance->table($table);
		$instance->where($where);
		
		return $instance;
	}

	/**
	* 设置查询字段
	*/
	public static function select($fields='*')
	{
		if (is_string($fields) && $fields!='*') $fields = explode(',', $fields);

		$instance = new static;
		$instance->_builder['type']   = 'select';
		$instance->_builder['fields'] = $fields;
		
		return $instance;	
	}

	/**
	* 更新数据
	*
	* @param string $table 表名 
	* @param array $set 更新数据 
	* @param array $where where条件
	* @return self
	*/
	public static function update($table,$set=NULL,$where=NULL)
	{
		$instance = new static;
		
		$instance->_builder['type'] = 'update';
		
		$instance->table($table);
		$instance->data($set);
		$instance->where($where);
		
		return $instance;
	}
	




	/**
	* 设置表
	*
	* @param string $table 表名 
	* @return self
	*/
	public function table($table)
	{
		$this->_builder['table'] = $table;
		return $this;
	}
	
	/**
	* 设置表 同table
	*
	* @param string $table 表名 
	* @return self
	*/
	public function from($table)
	{
		$this->table($table);
		return $this;
	}

	/**
	* 设置where条件
	*
	* @param string|array $where where条件 
	* @return self
	*/
	public function where($where)
	{
		$this->_builder['where'] = $where;
		return $this;
	}

	/**
	* 设置order
	*
	* @param string|array $order order条件 
	* @return self
	*/
	public function order($order)
	{
		$this->_builder['order'] = $order;
		return $this;
	}
	
	/**
	* 设置起始条数
	*
	* @param int $offset 起始条数 
	* @return self
	*/
	public function offset($offset)
	{
		$this->_builder['offset'] = $offset;
		return $this;
	}

	/**
	* 设置限制条件
	*
	* @param int $limit 限制条数
	* @param int $offset 起始条数
	* @return self
	*/
	public function limit($limit,$offset=0)
	{
		if(is_array($limit))
		{
			$offset = $limit[0];
			$limit = $limit[1];
		}

		$this->_builder['limit'] = $limit;
		$this->offset($offset);
		return $this;
	}

	/**
	* 设置数据
	*
	* @param array $data 数据 
	* @return self
	*/
	public function data($data)
	{
		$this->_builder['data'] = $data;
		return $this;
	}

	/**
	* 设置数据 同data
	*
	* @param array $data 数据 
	* @return self
	*/
	public function set($set)
	{
		$this->data($set);
		return $this;
	}

	/**
	* 设置数据 同data
	*
	* @param array $data 数据 
	* @return self
	*/
	public function values($values)
	{
		$this->data($values);
		return $this;	
	}

	/**
	* where条件生成SQL
	*
	* @param array $where where 条件
	* @return string where SQL
	*/
	public static function builder_where($where)
	{
		if(is_array($where))
		{
			$_tmp = '';
			foreach($where as $k=>$v)
			{
				if(is_array($v))
				{

					if(is_array($v[0]))
					{
						// array('uid'=>array(array('>',1),array('<',3)))  array('uid'=>array(array('>',1),'AND',array('<',3)))
                        for($i=0,$j=count($v); $i<$j; $i++)
						{
                            $_con = ' AND ';
                            
                            if( !is_array($v[$i]) && in_array(strtoupper(trim($v[$i])),array('AND','OR')) )
                            {
                                $_con = ' '.strtoupper(trim($v[$i])).' ';
                                $i++;
                            }
							    
                            $_tmp .= $_con."`{$k}` {$v[$i][0]} '".addslashes($v[$i][1])."'";
                        }
					}
					else
					{
						// array('uid'=>array('>',1))
						$_tmp .= " AND `{$k}` {$v[0]} '".addslashes($v[1])."'";
					}
				}
				else
				{
					// array('uid ='=>'1')
					list($k,$op) = explode(' ',trim($k));
					if(!$op) $op = '=';
					$_tmp .= " AND `{$k}` {$op} '".addslashes($v)."'";
				}
			}
			$where = substr($_tmp,5);
		}
		if($where) $where = 'WHERE '.$where;
		
		return $where;
	}
	
	/**
	* 获得SQL,同getSql
	*
	* @return string SQL
	*/
	public function get()
	{
		return $this->getSql();
	}

	/**
	* 获得SQL
	*
	* @return string SQL
	*/
	public function getSql()
	{
		extract($this->_builder);
		
		if(is_array($table))
			$table = '`'.implode('`',$table).'`';
		else
			$table = '`'.$table.'`';
		
		
		$where = \Builder::builder_where($where);
		
		if(is_array($order))
		{
			$_tmp = '';
			foreach($order as $k=>$v)
			{
				$_tmp .= ", `{$k}` {$v}";
			}
			$order = substr($_tmp,1);
		}
		if($order) $order = 'ORDER BY '.$order;
		

		if($offset && $limit)
		{
			$limit = "{$offset},{$limit}";
		}
		if($limit) $limit = 'LIMIT '.$limit;


		//---------------------------------------------------

		if( $type == 'insert' )
		{
			// table(data-k) (data-v)
			return "INSERT INTO {$table}(`".implode('`,`',array_keys($data))."`) VALUES('".implode("','",$data)."')";
		}

		if( $type == 'delete' )
		{
			return "DELETE FROM {$table} {$where} {$limit}";
		}

		if( $type == 'select' )
		{
			// fields table where group order limit 
			if($distinct) $distinct = 'DISTINCT';
			
			if(!$fields) $fields = '*';
			if(is_array($fields)) $fields = '`'.implode('`,`',$fields).'`';

			return "{$distinct} SELECT {$fields} FROM {$table} {$where} {$group} {$order} {$limit}";
		}
		
		if( $type == 'update' )
		{
			$_tmp = '';
			foreach($data as $k=>$v)
			{
                if(is_array($v))
                {
                    if(is_numeric($v[1]))
                    {
                        if(in_array($v[0],array('+','-','*','/')))
                        {
                             $_tmp .= ",`{$k}`=`{$k}` {$v[0]} {$v[1]}";
                        }
                        else
                        {
                            exit;
                        }
                    }
                    else
                    {
                        exit;
                    }
                }
                else
                {
                    $_tmp .= ",`{$k}`='{$v}'";
                }
			}
			$set = 'SET '.substr($_tmp,1);
			
			return "UPDATE {$table} {$set} {$where}";
		}
	}
}