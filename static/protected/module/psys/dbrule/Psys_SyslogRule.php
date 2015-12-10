<?php

class PSys_SyslogRule extends PSys_DbAbstractRule
{
	public function __construct()
	{
		parent::__construct();
		$this->SetTable("rha_syslog");
	}
}

?>