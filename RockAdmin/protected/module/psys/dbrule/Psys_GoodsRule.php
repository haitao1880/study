<?php
/**
 * 商品
 * @author Administrator
 *  */
class Psys_GoodsRule extends Psys_DbAbstractRule
{
	public function __construct()
	{
		parent::__construct();
		$this->_dbprefix = 'rhi_';
		$this->SetDb("db-rht_idc");
		$this->SetTable("vgoods");
	}

	public function GetLastId(){
		$sql = 'select id from '.$this->GetTableName().' order by id desc limit 1;';
		return $this->FetchColOne($sql);
	}
}