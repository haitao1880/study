<?php
/**
* 摘    要: 短信管理数据逻辑                                                      
*/
class Psys_SmsRule extends Psys_DbAbstractRule
{
	public function __construct()
	{
		parent::__construct();
		//$this->SetDb("db-rht_idc");
		$this->SetTable("rha_smsrecharge");
	}
	//获取时间内短信充值量
	public function getSmsRechargeNum($stime,$etime,$type = 1)
	{
		$num = 0;
		$sql = 'select sum(nume) as num FROM ' .$this->_table . ' where type='.$type.' and ctime > '.$stime.' and ctime <= '.$etime;
		$list = $this->Query($sql);
		if(!empty($list[0]['num'])){
			$num = $list[0]['num'];			
		}
		return $num;
	}
	//获取时间内短信发送量
	public function getSmsIdclogNum($stime,$etime,$type = 1)
	{
		$num = 0;
		$this->SetDb("db-rht_idc");
		$sql = 'select count(*) as num FROM rhi_smsclog where type='.$type.' and ctime > '.$stime.' and ctime <= '.$etime;
		$list = $this->Query($sql);	
		if(!empty($list[0]['num'])){
			$num = $list[0]['num'];
		}
		return $num;
	}	
}

?>