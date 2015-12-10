<?php
class DbSelect
{
	public $sqlstring="";
	
	/**
	 * 构造复杂SQL
	 * $objsql = new DbSelect("a.userid,b.groupname");
	 * @param string $field 要显示的字段 为空则为"*"
	 */
	public function __construct($field)
	{
		$field = trim($field)== '' ? '*':trim($field);
		$this->sqlstring = "SELECT ".$field;
	}
	
	/**
	 * 相当于SQL的FROM
	 * $objsql->From("user_table",
	 * @param string $tbname
	 * @param string $aliasname 别名
	 */
	public function From($tbname,$aliasname='')
	{
		$tbname = $this->GetPreFix($tbname);
		$this->sqlstring .= " FROM ".$this->AddQuote($tbname);
		if($aliasname)
		{
			$this->sqlstring .= " AS ".$aliasname;
		}
	}
	
	public function Innerjoin($tbname,$aliasname,$onwhere)
	{
		
	}
	
	public function __Destruct()
	{
		$this->sqlstring = '';
	}
	
	protected function GetPreFix($tbname)
	{		
		global $G_X;
		
		$tbname = $G_X['db']['tb_prefix'].$tbname;
		
		return $tbname;
	}
	
	protected function AddQuote($key)
	{
		$tmp = explode(".", $key);
    	foreach ($tmp as $k=>&$v)
    	{
    		$v = "`$v`";
    	}
    	$newkey = implode(".", $tmp);
    	return $newkey;
	}
}

class DbWhere
{
	protected $key;
	protected $val;
	protected $zh;//or and like in
	protected $sqlwhere;
	
	const PASOR   = "OR";
	const PASAND  = "AND";
	const PASLIKE = "LIKE";
	const PASIN   = "IN";
	
	public function __construct(){
		$this->sqlwhere = '';
	}
	
	public function Where($k,$v,$kvassoc,$zh)
	{
		if($this->sqlwhere != '')
		{
			$this->sqlwhere .= $zh . "(".$this->AddQuote($k).")";
		}else{
			
		}
	}
	
	protected function AddQuote($key)
	{
		$tmp = explode(".", $key);
    	foreach ($tmp as $k=>&$v)
    	{
    		$v = "`$v`";
    	}
    	$newkey = implode(".", $tmp);
    	return $newkey;
	}
}
?>