<?php
/**
 * 商品
 * @author Administrator
 *  */
class Psys_MallorderRule extends Psys_DbAbstractRule
{
	public function __construct()
	{
		parent::__construct();
		$this->_dbprefix = 'rhi_';
		$this->SetDb("db-rht_idc");
		$this->SetTable("mallorder");
	}
	
	public function GetGoodsName($data)
	{
		$this->SetTable("vgoods");
		$sql = 'select name from '.$this->GetTableName().' where id =:goodsid';
		$one = $this->FetchColOne($sql,$data);
		$this->SetTable("mallorder");
		return $one;
	}

}