<?php

class Psys_UserRule extends Psys_DbAbstractRule
{
	public function __construct()
	{
		parent::__construct();
		$this->SetDb("db-rht_idc");
		$this->SetTable("rhi_user");
	}
	
	public function getAwardSum($where){
		$this->SetTable("rhi_useraward");
		$sql = 'select sum(moeny) as sumaward from '.$this->GetTableName().' where '.$where;
		$ret = $this->FetchColOne($sql);
		$this->SetTable("rhi_user");
		return $ret;
	}
}