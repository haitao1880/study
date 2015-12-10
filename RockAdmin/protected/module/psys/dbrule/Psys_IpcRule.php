<?php
/**
* 摘    要: 设备管理数据逻辑                                                      
*/
class Psys_IpcRule extends Psys_DbAbstractRule
{
	public function __construct()
	{
		parent::__construct();
		$this->SetTable("rha_ipc");
	}
}

?>