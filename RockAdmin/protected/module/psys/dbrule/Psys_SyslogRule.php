<?php

class Psys_SyslogRule extends Psys_DbAbstractRule
{
	public function __construct()
	{
		parent::__construct();
		$this->SetTable("rha_syslog");
	}
}

?>