<?php

class Psys_RegionRule extends Psys_DbAbstractRule
{
	public function __construct()
	{
		parent::__construct();
		$this->SetDb("db-rht_idc");
		$this->SetTable("rhi_region");
	}
}

?>