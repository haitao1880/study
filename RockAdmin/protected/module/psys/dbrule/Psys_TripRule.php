<?php
/**
* 摘    要: 线路管理数据逻辑                                                      
*/
class Psys_TripRule extends Psys_DbAbstractRule
{
	public function __construct()
	{
		parent::__construct();
		$this->SetDb("db-rht_trip");
		$this->SetTable("rht_trainno");
	}
	
}

?>