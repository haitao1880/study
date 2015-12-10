<?php

class Psys_MemberUserRule extends Psys_DbAbstractRule
{
	public function __construct()
	{
		parent::__construct();
		$this->SetDb("db-rht_idc");
		$this->SetTable("rhi_account");
	}

	//获取会员查询列表
	public function SearchList($username,$email,$regtime,$logintime,$page,$pagesize){
		
		$limiter = ($page-1)*$pagesize;
		$sql = 'select * from '.$this->_table;
		$sql .= ' where '.$username.$email.$regtime;
		$sql .= $logintime;
		$sql .= ' limit '.$limiter.','.$pagesize;

		$c_sql = "select count(*) as allnum from ($sql) as t";
		$allnum = $this->FetchColOne($c_sql);
		$list = $this->Query($sql);	
		return array('allrow'=>$list,'allnum'=>$allnum);
	}
}
?>