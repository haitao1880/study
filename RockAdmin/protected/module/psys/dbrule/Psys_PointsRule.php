<?php
/**
 * 积分
 * @author Administrator
 *  */
class Psys_PointsRule extends Psys_DbAbstractRule
{
	public function __construct()
	{
		parent::__construct();
		$this->_dbprefix = 'rhi_';
		$this->SetDb("db-rht_idc");
		$this->SetTable("points");
	}

}