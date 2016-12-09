<?php
namespace core\component;
/**
 * ActiveRecord 
 *
 * @author LM <sostart.net@gmail.com>
 * @package system
 * @since 1.0
 */
class ActiveRecord
{
	public $tableName;
	public $primaryKey = 'id';

	public function __construct($tableName=null)
	{
		if($tableName) $this->setTableName( $this->realTable($tableName) );
	}

	/**
	 * 获取对应数据库的表名
	 *
	 * @return string
	 */
	public function getTableName()
	{		
		if( $this->tableName )
		{
			return $this->tableName;
		}
		else
		{
			return $this->setTableName( $this->realTable( get_called_class() ) );
		}
	}

	/**
	 * 解析类名为数据库表名
	 *
	 * 类名-表名规则 例  UserLogin 对应表名为 user_login
	 * 
	 * @param string $tableName
	 * @return string
	 */
	public static function realTable($tableName)
	{
		return strtolower(implode('_', preg_split("/(?=[A-Z])/", $tableName, null, PREG_SPLIT_NO_EMPTY)));
	}

	/**
	 * 设置数据库表名
	 *
	 * @param string $tableName
	 * @return object 
	 */
	public function setTableName($tableName)
	{
		return $this->tableName = $tableName;
	}
	
	/**
	 * 查找记录
	 *
	 * @param mix $id
	 * @return array 
	 */
	public function find($id=false,$fields='*',$limit=1,$order=NULL)
	{
		if($id===false)
			$where = NULL;
		else if( !is_array($id) && is_numeric($id) ) 
			$where = array( $this->primaryKey => $id );
		else
			$where = $id;
		
		$rs = \DB::query(
			\Builder::select($fields)->from($this->getTableName())->where($where)->order($order)->limit($limit)->getSql()
		);
		
		if($rs===false) return false;
		
		return $limit===1?$rs->fetch():$rs->fetchAll();
	}

	/**
	 * 查找记录
	 *
	 * @return array 
	 */
	public function findAll($where=NULL,$fields='*',$limit=NULL,$order=NULL)
	{
		return $this->find($where,$fields,$limit,$order);
	}

	/**
	 * 统计记录数
	 *
	 * @return int 
	 */
	public function countAll($where='')
	{
		$where = \Builder::builder_where($where);
		return \Common::array_value('count',\DB::query('SELECT count(*) AS `count` FROM `'.$this->getTableName().'` '.$where)->fetch());
	}

	/**
	 * 增加一条记录
	 *
	 * @param array $data
	 * @param mix $where
	 * @return array 
	 */
	public function add($data)
	{		
		if( $rs = \DB::execute(
			\Builder::insert($this->getTableName())->values($data)->getSql()
		))
		{
			return ($id = \DB::get_insert_id()) ? $id : true ;
		}
		else
		{
			return $rs;
		}
	}

	/**
	 * 更新一条记录
	 *
	 * @param array $data
	 * @param mix $where
	 * @return array 
	 */
	public function update($set,$where)
	{
		if( !$where ) return false;
		if( is_numeric($where) ) $where = array( $this->primaryKey => $where );

		return \DB::execute(
			\Builder::update($this->getTableName())->set($set)->where($where)->getSql()
		);	
	}

	/**
	 * 更新或增加一条记录
	 *
	 * @param array $data
	 * @param mix $where
	 * @return array 
	 */
	public function save($data,$where)
	{
		if( !$where ) return false;
		if( is_numeric($where) ) $where = array( $this->primaryKey => $where );

		if($this->find($where))
		{
			return $this->update($data,$where);
		}
		else
		{
			return $this->add(array_merge($where,$data));
		}
	}

	/**
	 * 删除记录
	 *
	 * @param array $data
	 * @param mix $where
	 * @return array 
	 */
	public function remove($where)
	{
		if( !$where ) return false;
		if( is_numeric($where) ) $where = array( $this->primaryKey => $where );

		return \DB::execute(
			\Builder::delete($this->getTableName())->where($where)->getSql()
		);
	}
}