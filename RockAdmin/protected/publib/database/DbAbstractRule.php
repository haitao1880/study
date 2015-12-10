<?php
/**
 *	数据逻辑抽象类
 *	@author		yqren
 *	@copyright	2011-2012
 *	@version	2.0
 *	@package	PAS2
 *
 *	$Id: DbAbstractRule.php 53 2014-03-27 09:06:56Z tony_ren $
 */
$curdir = dirname(__FILE__).DIRECTORY_SEPARATOR;
require_once $curdir.'DbFactory.php';
require_once $curdir.'DbException.php';

/**
 * 数据组合层抽象类
 * @author xiuluo
 *
 */
class DbAbstractRule
{
    protected $_db;
    protected $_db_r;
    protected $_dbprefix;
    protected $_table;
    protected $_dbconfig = 'db';

    public function __construct()
    {
        global $G_X;
        $this->_dbprefix = $G_X[$this->_dbconfig]['tb_prefix'];
        //$this->_table = $tbname;
    }
    
     /**
     * 获得表名前缀
     */
    public function GetPreFix()
    {
        return $this->_dbprefix;
    }

    /**
     * 获得表名
     * @param boolean $containspre 是否包含表名前缀
     * @return string 表名
     */
    public function GetTableName($containspre = true)
    {
        if($containspre)
        {
            return $this->GetPreFix() . $this->_table;
        }else{
            return $this->_table;
        }
    }

    /**
     * 获得DB类
     * @return pdo db
     */
    public function GetDb($isReadOnly=false)
    {
        global $G_X;
        if(isset($G_X['db_read'])){
            $readOnlyFunctions=array(
    	    	'GetList','GetOne','GetOneExt','FetchCol',
    	    	'FetchColOne','FetchRow','FetchAssoc','IsExists');
            $backtrace=debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            if(in_array($backtrace[1]['function'], $readOnlyFunctions))$isReadOnly=true;
            if($isReadOnly){
                if(!$this->_db_r)
                {
                    $this->_db_r = DbFactory::Create('db_read');
                }
                return $this->_db_r;
            }
        }
         
         
         
        if(!$this->_db)
        {
            $this->SetDb($this->_dbconfig);
        }
        return $this->_db;
    }

    /**
     * 设置DB类 pdo_mysql
     * @param string $configname 对应config.php中的db
     */
    public function SetDb($configname = 'db')
    {
    	$this->_dbconfig = $configname == '' ? $this->_dbconfig : $configname;
        $this->_db = DbFactory::Create($this->_dbconfig);
    }
    
    /**
     * 获得数据库的类型
     */
    public function GetDbType()
    {
    	global $G_X;
    	
    	return $G_X[$this->_dbconfig]['dbtype'];
    }

    /**
     * 不含前缀的表名
     * @param string $tbname
     */
    public function SetTable($tbname)
    {
        $this->_table = $tbname;
    }

    /**
     * 获得当前查询的SQL
     */
    public function GetQueryString()
    {
        return $this->GetDb()->ToString();
    }

    /**
     * 格式化查询条件
     * @param  mixed $where 查询条件，可以是数组，也可以是字符串
     * @return string
     */
    protected function _WhereExpr($where=''){
        return $this->GetDb()->_WhereExpr($where);
    }


    /**
     * 格式化查询字段
     * @param  mixed $fields 查询字段，可以是数组，也可以是字符串
     * @return string
     */
    protected function _FieldsExpr($fields='*'){
        return $this->GetDb()->_FieldsExpr($fields);
    }

    /**
     * 根据条件获得单条数据
     * @param string $field
     * @param array $pk_arr 列名=>列值
     */
    public function GetOne($field,array $pk_arr)
    {
    	$dbtype = $this->GetDbType();
    	if($dbtype == 'mysql'){
        	$sql = "SELECT $field FROM ".$this->GetTableName();//." where $pkid='".addslashes($pkv)."'";
    	}else{
    		$sql = "SELECT TOP 1 $field FROM ".$this->GetTableName();
    	}
        $where = '';//$this->_WhereExpr($pk_arr);

        foreach($pk_arr as $k=>$v)
        {
            $isunset = false;
            if($where == '')
            {
                $where .= $this->GetDb()->FmtFieldWithParam($k,$v,$isunset);
            }else{
                $where .= " AND ".$this->GetDb()->FmtFieldWithParam($k,$v,$isunset);
            }
            if($isunset)
            {
                unset($pk_arr[$k]);
            }
            	
        }

        //print_r($pk_arr);

        if($where != '')
        {
            $sql .= " WHERE ".$where;
        }
        if($dbtype == 'mysql'){
        	$sql .= " LIMIT 0,1";
        }
//         echo $sql;
        return $this->GetDb()->FetchRow($sql,array_values($pk_arr),null);
    }

    /**
     * 获得单条数据
     * @param string $field 字段名
     * @param string $where 不含WHERE的条件串，用于复杂条件
     */
    public function GetOneExt($field,$where)
    {
        $sql = "select $field From ".$this->GetTableName();
        if($where != '')
        {
            $sql .= " where ".$where;
        }

        return $this->GetDb()->FetchRow($sql,null,null);
    }

    /**
     * 执行参数化的SQL
     * @param string $sql select * From user where userid=?
     * @param array $bind array(1)
     * @return array
     */
    public function QueryAll($sql,array $bind) {
        return $this->GetDb()->FetchAll($sql, $bind,null);
    }


    /**
     * 执行单条SQL语句
     * @param string $sqlstr
     * @return array
     */
    public function Query($sqlstr,array $bind=array()){
        if(!$sqlstr){return false;}
        $sqlstr   = trim($sqlstr);
        $test_str = strtolower(substr($sqlstr,0,10));//获取判断类型的关键字

        if(strstr($test_str,'select')){//如果是查询
            return $this->GetDb()->FetchAll($sqlstr,$bind);
        }else{
            $stmt = $this->GetDb()->Query($sqlstr,$bind);
            $result = $stmt->rowCount();
            if(strstr($test_str,'Insert')){//如果是插入
                return $this->GetDb()->GetConnection()->LastInsertId();
            }
            //否则就是改和删或插入失败的情况执行操作影响了几条记录.
            return $result;
        }
    }

    /**
     * 插入一条数据
     * @param array $data
     * @param boolean $Retun_InsertID 返回自增ID？
     * @return 成功则返回自增ID
     */
    public function Insert($data=array(),$Retun_InsertID=true){
        if(!$data){return false;}
        $r=$this->GetDb()->Insert($this->GetTableName(),$data,$Retun_InsertID);
        //$this->unsetCache($this->GetTableName());
        return $r;
        
        
        
    }

    /**
     * 删除一条数据
     * @param mixed $where array("userid"=>1)或userid=1
     * @return 删除的条数
     */
    public function Delete($where){
        if(!$where){ return false;}
        //$this->unsetCache($this->GetTableName());
        return $this->GetDb()->Delete($this->GetTableName(),$where);
    }


    /**
     * 修改
     * @param array $data array("chinesename"=>"中国人")
     * @param mixed $where array(1,2)或不含WHERE的SQL
     * @return 更改成功的数量
     */
    public function Update(array $data,$where=''){
        if(!$data){return false;}
        
        //$backtrace=debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        //print_r($backtrace);
        
        $r=$this->GetDb()->Update($this->GetTableName(),$data,$where);
        
        //$this->unsetCache($this->GetTableName());
        
        
        return $r;
    }

    /**
     * 更新关联表数据
     * @param array $del_arr 要删除的数据，形如array("pk"=>"id","pv"=>array())
     * @param array $add_arr 要添加的数据，开如array(array("id"=>"","cnname"=>""))
     * @param string $tbname 关联表名，为空则为当前RULE表名,不含前缀
     * @return array array("delnum"=>0,"addnum"=>0);
     */
    public function UpdateAssoc(array $del_arr,array $add_arr,$tbname="")
    {
        //var_dump($del_arr);
        //var_dump($add_arr);
        //var_dump($tbname);
        if($tbname == '')
        {
            $tbname = $this->GetTableName();
        }else{
            $tbname = $this->GetPreFix().$tbname;
        }
        $result = array("delnum"=>0,"addnum"=>0);
        if(count($del_arr) > 0 && count($del_arr['pv']) > 0)
        {
            $sql = "`".$del_arr['pk']."` in ('".implode("','", $del_arr['pv'])."')";
            $sql = "Delete FROM ".$tbname." WHERE ".$sql;
            $stmt = $this->GetDb()->Query($sql);
            $result['delnum'] = $stmt->rowCount();
        }
        foreach($add_arr as $k=>$v)
        {
            $num = $this->GetDb()->Insert($tbname,$v,true);
            if($num > 0)
            {
                $result['addnum'] += 1;
            }
        }
        //$this->unsetCache($this->GetTableName());
        return $result;
    }
    
    
    
    /**
     *获得列表数
     * @param mixed $where array("userid"=>1)或userid=1
     * @param string $fields 显示字段
     * @param string $order 不含order by
     * @param int $page_size 分页大小
     * @param int $cur_page 当前页数
     * @return array("allrow"=>array(),"allnum"=>0)
     */
    public function GetList($where='',$fields='*',$order='',$page_size=0,$cur_page=0){
       $r=null;$setcache=false;
       /*
        if($this->_cache){
            $whereStr=is_array($where)?serialize($where):$where;
            $key=md5($this->GetTableName().$whereStr.$fields.$order.$page_size.$cur_page."ss");
            $r=$this->getCache($key);
        }*/
        if(empty($r)){
            
            $r=$this->GetDb()->GetList($this->GetTableName(),$where,$fields,$order,$page_size,$cur_page);
            $setcache=true;
        }
        //if($this->_cache&&$setcache){
        //       $this->setCache($key, $r);
        //}
        return $r;
    }

    /**
     * 符合条件的记录数
     * @param array $where
     * @return int
     */
    public function IsExists(array $where)
    {	
        return $this->GetDb()->IsExists($this->GetTableName(),$where);
    }

    /**
     * 返回第一列.
     * @param string $field
     * @param array $where
     */
    public function FetchCol($field,array $where)
    {
        $sql = "SELECT $field FROM ".$this->GetTableName();
        $where = $this->WhereExpr($where);
        if($where){ $sql.=' WHERE 1 '.$where;}

        return $this->GetDb()->FetchCol($sql,array());
    }

    /**
     * 返回第一行第一列值
     * Fetches the first column of the first row of the SQL result.
     *
     * @param string $sql
     * @param mixed $bind Data to bind into SELECT placeholders.
     * @return string
     */
    public function FetchColOne($sql, $bind = array())
    {
        return $this->GetDb()->FetchColOne($sql, $bind);
    }

    /**
     * 返回一行
     * @param string $sql
     * @param array $bind
     * @param mixed $fetchMode
     */
    public function FetchRow($sql,$bind=array(), $fetchMode = null)
    {
        return $this->GetDb()->FetchRow($sql, $bind, $fetchMode);
    }

    /**
     * 返回键值对，如select id,cnname From table,
     * iskeyvalue=false则返回array("id的值"=>array("id的值","cnname的值"))
     * iskeyvalue=true则返回array("id的值"=>"cnname的值")
     * @param array $where
     * @param string $field
     * @param boolean $iskeyvalue 是否返回键值对
     */
    public function FetchAssoc(array $where,$field,$iskeyvalue = false)
    {
        $sql = "SELECT $field FROM ".$this->GetTableName();
        $where = $this->WhereExpr($where);
        if($where){ $sql.=' WHERE 1 '.$where;}
        return $this->GetDb()->FetchAssoc($sql, array(),$iskeyvalue);
    }

    /**
     * 格式化WHERE条件
     * @param mixed $where array("id"=>1)或" and id=1"
     * 推荐用array
     */
    public function WhereExpr($where)
    {
        return $this->GetDb()->WhereExpr($where);
    }
}