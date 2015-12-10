<?php
class Psys_ActivityRule extends Psys_DbAbstractRule
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
		//wifi、首页、总下载、活动下载、抽奖人数只统计UV
		$isallentrance = 0;
		if (in_array($action,array('userindexdown','userhqgdown','userdmbjdown','useractivedetailsdown'))) {
			$type = 'pv';
		}
		if ($from == 'all') {
			$from = "spreadwindow' OR `from`= 'spreadnav";
		}

		if ($type == 'pv') {
			$num = 'sum(num)';
		}elseif($type == 'uv'){
			$num = 'count(DISTINCT phone)';
		}		
		
		if ($action == 'opentrainapp') {
			$sql = "SELECT sum(newuser) as num  FROM rha_open_trainapp_usernum WHERE (
										date BETWEEN '$sdate' AND '$edate' )";
			if ($hour) {
				$sql .= " AND `hour` = '$hour'";
			}
		}else{
			$sdate=str_replace('-','_',$sdate);
			$edate=str_replace('-','_',$edate);
			if ($action == 'all') {
				$action = "spreadwindow' OR action = 'spreadnav";
				$isallentrance = 1;				

			}elseif($action == 'userindexdown'){
				$action = "spreadapp' AND downpage = 'userindex";

			}elseif($action == 'userhqgdown'){
				$action = "spreadapp' AND downpage = 'userhqg";

			}elseif($action == 'userdmbjdown'){
				$action = "spreadapp' AND downpage = 'userdmbj";
			}elseif($action == 'useractivedetailsdown'){
				$action = "spreadapp' AND downpage = 'useractivedetails";
			}
			
			if (in_array($action,array('spreadwindow','spreadnav'))) {
				$isallentrance = 1;					# code...
			}
			 
			$sql = "SELECT $num as num FROM rha_spread_trainapp WHERE (
										date BETWEEN '$sdate' AND '$edate' )";
			if (!$station) {

				if ($hour) {
					$sql .= " AND `hour` = '$hour'";
				}
				$sql .= "  AND numtype = '$type' AND (action = '$action') ";
				if (!$isallentrance) {
					$sql .= "  AND (`from`='$from')";
				}
			}else{				
				if ($hour) {
					$sql .= " AND `hour` = '$hour'";
				}

				$sql.= "  AND stationid = $station AND numtype = '$type' AND (action = '$action')";
				if (!$isallentrance) {
					$sql .= "  AND (`from`='$from')";
				}
			}
		}
		//echo $sql.PHP_EOL;	
		$allrow = $this->Query($sql);
		if (!$allrow[0]['num']) {
			$allrow[0]['num'] = 0;
		}
		return $allrow[0]['num'];

	}
}