<?php
class Psys_ActivityModel extends Psys_AbstractModel
{
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * 新用户抽奖活动统计
	 */
	public function NewUserActivity($sdate,$edate,$station,$hour,$type,$action,$from)
	{
		$db = new Psys_ActivityRule();
		return $db -> NewUserActivity($sdate,$edate,$station,$hour,$type,$action,$from);
	}
	
}