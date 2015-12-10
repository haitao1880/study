<?php
/**
* Copyright(c) 2013
* 日    期:2013年11月21日
* 文 件 名:AbstractModel.php
* 创建时间:下午4:32:20
* 字符编码:UTF-8
* 版本信息:$Id: AbstractModel.php 10 2014-06-13 07:03:39Z tony_ren $
* 修改日期:$LastChangedDate: 2014-06-13 15:03:39 +0800 (周五, 13 六月 2014) $
* 最后版本:$LastChangedRevision: 10 $
* 修 改 者:$LastChangedBy: tony_ren $
* 版本地址:$HeadURL: http://192.168.1.100:12000/svn/rockgame/trunk/code/gamewww/protected/publib/abstract/AbstractModel.php $
* 摘    要:业务逻加抽象层
*/
class AbstractModel
{
	protected $clsname = '';
	protected $_isdebug = false;

	/**
	 * MODEL层所有用到的class,别的MODEL,别的RULE
	 * @var mixed 一般为class
	 */
	protected $objarr = array();
	
	/**
	 * 获得需要使用的对象，为空则为本model对应的RULE.
	 * 如：PItem_CheckModel，对应的rule则为PItem_ItemRule
	 * @param string $clsname RULE或别的MODEL类名
	 * @return mixed
	 */
	public function GetClassObj($clsname = '')
	{
		if($clsname == '')$clsname = $this->clsname;
		
		if(!array_key_exists($clsname, $this->objarr) || !$this->objarr[$clsname])
		{
			$this->SetClassObj($clsname);
		}
		return $this->objarr[$clsname];
	}
	
	/**
	 * 设置使用的类
	 * @param mixed $clsname 可以是类名，也可以是类实例
	 */
	public function SetClassObj($clsname = '')
	{
		if(is_object($clsname))
		{
			$clsname2 = get_class($clsname);
			$this->objarr[$clsname2] = $clsname;
		}else{
			if($clsname == '')$clsname = $this->clsname;
			$this->objarr[$clsname] = new $clsname();
		}
	}
	
	public function __destruct()
	{
		$this->objarr = null;
	}
	
/**
     * 设置缓存key
     * @param unknown_type $key
     */
    public function  CacheInIt($key=array(),$cachetime=604800){
        
        $this->GetClassObj()->CacheInIt($key,$cachetime);
    }
	
		
	/**
	 * 打开调试状态
	 */
	public function SetDebug()
	{
		$this->_isdebug = true;
	}
	
	/**
	 * 获得当前的调试状态
	 * @return boolean
	 */
	public function GetDebug()
	{
		return $this->_isdebug;
	}
	
	/**
	 * 构造函数
	 * @param string $cls Rule类名
	 * @param string $model MODEL名，PSys,PItem,PTech,PPaper,PExam,PAnalyse
	 */
	public function __construct()
	{
		//$this->clsname = $model."_".$cls."Rule";
		$clsname = get_called_class();
		$clsname = str_replace("Model","Rule",$clsname);
		$this->clsname = $clsname;
	}
	
	/**
	 * 取得列表(一般查询)
	 * @param mixed $where
	 * @param string $orderby 不含order by
	 * @param int $page
	 * @param int $pagesize
	 * @param string $field 字段筛选
	 * @return array("allrow"=>array(),"allnum"=>0);:
	 */
	public function GetList($where,$orderby='',$page=0,$pagesize=0,$field="*",$tbname='')
	{
		$cr = $this->GetClassObj();
		if($tbname != '')
		{
			$srctb = $cr->GetTableName(false);
			$cr->SetTable($tbname);
			$rtn = $cr->GetList($where,$field,$orderby,$pagesize,$page);
			$cr->SetTable($srctb);
		}else{
			$rtn = $cr->GetList($where,$field,$orderby,$pagesize,$page);
		}
		
		if($this->_isdebug )
		{
			echo $cr->GetQueryString();
		}
		return $rtn;
	}
	
	/**
	 * @param string $configname 配置文件中的数据库连接信息
	 */
	public function SetDb($configname)
	{
		$cr = $this->GetClassObj();
		$cr->SetDb($configname);
	}
	
	/**
	 * 映射到rule层去格式化查询条件为字符串，以实现更多的组合
	 * @param mixed $where
	 * @return string
	 */
	public function WhereExpr($where){
	    $cr = $this->GetClassObj();
	    $result = $cr->_WhereExpr($where);
	    return $result;
	}
	
	/**
	 * 取得单个查询
	 * @param mixed $where array("courseid"=>$id)
	 */
	public function GetOne($where,$field="*",$tbname='')
	{
		$cr = $this->GetClassObj();
		if($tbname != '')
		{
			$srctb = $cr->GetTableName(false);
			$cr->SetTable($tbname);
			$rtn = $cr->GetOne($field, $where);
			$cr->SetTable($srctb);
		}else{
			$rtn = $cr->GetOne($field, $where);
		}
	
		if($this->_isdebug)
		{
			echo $cr->GetQueryString();
		}
		return $rtn;
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
		$cr = $this->GetClassObj();
		$result = $cr->UpdateAssoc($del_arr,$add_arr,$tbname);
		if($this->_isdebug)
		{
			echo $cr->GetQueryString();
		}
		
		return $result;
	}
	
	/**
	 * 更新数据
	 * @param array $data 要更新的数据，key字段名，VAL是字段值
	 * @param array $where 筛选条件
	 * @param string $tbname 目前被当作表名来用，在日后版本中可能会被替，因为增加了类的耦合度
	 * @return Ambiguous
	 */
	public function UpdateOne(array $data,$where,$tbname='')
	{
		$cr = $this->GetClassObj();
		if($tbname != '')
		{
			$srctb = $cr->GetTableName(false);
			$cr->SetTable($tbname);
			$num = $cr->Update($data,$where);
			$cr->SetTable($srctb);
		}else{
			$num = $cr->Update($data,$where);
		}
			
	
		if($this->_isdebug)
		{
			echo $cr->GetQueryString();
		}
		
		return $num;
	}
	
	/**
	 * 获得符合条件的记录总数
	 * @param mixed $where array(推荐)或 string
	 * @param str $tbname
	 * @return num
	 */
	public function IsExists($where,$tbname='')
	{
		$rtn = 0;
		$cr = $this->GetClassObj();
		if($tbname != '')
		{
			$srctb = $cr->GetTableName(false);
			$cr->SetTable($tbname);
			$rtn = $cr->IsExists($where);
			$cr->SetTable($srctb);
		}else{
			$rtn = $cr->IsExists($where);
		}
		
		
		if($this->_isdebug)
		{
			echo $cr->GetQueryString();
		}
		
		return $rtn;
	}
	/**
	 * 新增一条数据
	 * @param array $data
	 * @param string $tbname
	 * @return num
	 */
	public function AddOne(array $data,$tbname='')
	{
		$cr = $this->GetClassObj();
		if($tbname != '')
		{
			$srctb = $cr->GetTableName(false);
			$cr->SetTable($tbname);
			$id = $cr->Insert($data);
			$cr->SetTable($srctb);
		}else{
			$id = $cr->Insert($data);
		}
		
	
		if($this->_isdebug)
		{
			echo $cr->GetQueryString();
		}
		return $id;
	}
	
	/**
	 * 删除单个
	 * @param array $pk_arr
	 * @param string $tbname 为空则为RULE默认表名，否则为设定值
	 * @return num 影响的行数
	 */
	public function DeleteOne(array $pk_arr,$tbname='')
	{
		$cr = $this->GetClassObj();
		if($tbname != '')
		{
			$srctb = $cr->GetTableName(false);
			$cr->SetTable($tbname);
			$num = $cr->Delete($pk_arr);
			$cr->SetTable($srctb);
		}else{
			$num = $cr->Delete($pk_arr);
		}
	
		if($this->_isdebug)
		{
			echo $cr->GetQueryString();
		}
		return $num;
	}
	/**
	 * 返回键值对，如select id,cnname From table,
	 * iskeyvalue=false则返回array("id的值"=>array("id的值","cnname的值"))
	 * iskeyvalue=true则返回array("id的值"=>"cnname的值")
	 * @param array $where
	 * @param string $field
	 * @param boolean $iskeyvalue 是否返回键值对
	 */
	public function FetchAssoc(array $where,$field,$iskeyvalue = false,$tbname='')
	{
		$cr = $this->GetClassObj();
		if($tbname != '')
		{
			$srctb = $cr->GetTableName(false);
			$cr->SetTable($tbname);
			//public function FetchAssoc($sql, $bind = array())
			$result = $cr->FetchAssoc($where,$field,$iskeyvalue);
			$cr->SetTable($srctb);
		}else{
			$result = $cr->FetchAssoc($where,$field,$iskeyvalue);
		}
	
		if($this->_isdebug)
		{
			echo $cr->GetQueryString();
		}
		return $result;
	}
	
	/**
	 * 获得当前查询的SQL
	 */
	public function GetQueryString()
	{
		$cr = $this->GetClassObj();
		return $cr->GetQueryString();
	}
	
	/**
	 * 添加系统日志
	 * @param string $Message 系统日志
	 * @param array $UserInfo 用户信息
	 */
	public function AddSysLog($Message,array $UserInfo = array())
	{
		$cr = $this->GetClassObj();
		if(count($UserInfo) == 0)
		{
			$UserInfo = XSession::Get("Cur_X_User");
		}
		$data['CreateUid']	= $UserInfo['id'];
		$data['Creator']	= $UserInfo['nickname'];
		$data['OpContent']	= $Message;
		$data['CreateTime']	= time();
		$data['OpUrl']		= @$_SERVER['REQUEST_URI'];
		
		return $cr->Insert($data);
	
	}
	
	/**
	 * 添加用户操作日志
	 * @param string $msg
	 * @param array $userinfo
	 */
	public function AddUserLog($msg,array $userinfo)
	{
		
		
	}
}
?>