<?php
class Psys_ResourceRule extends Psys_DbAbstractRule
{
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * 游戏或app下载概况数据
	 * @param [type] $sdate   起始时间
	 * @param [type] $edate   结束时间
	 * @param [type] $station 车站id
	 * @param [type] $type    类型
	 */
	public function DownAppData($sdate,$edate,$station,$type,$page,$pagesize,$is_graph=0){
		$parms = array($sdate,$edate,$type);
		$offset = ($page-1)*$pagesize;
		if ($station) {
			array_push($parms,$station);
			$sql = "SELECT	
						appname,
						stationid,
						appid,
						sum(num) as num
					FROM
						rha_downapp
					WHERE
						(date BETWEEN ? AND ? )
					AND type = ?
					AND stationid = ?
					AND action = 'downapp'
					GROUP BY
						appid
					ORDER BY sortid ASC";
		}else{
			$sql = "SELECT	
						appname,
						appid,
						sum(num) as num
					FROM
						rha_downapp
					WHERE
						(date BETWEEN ? AND ? )
					AND type = ?
					AND action = 'downapp'
					GROUP BY
						appid
					ORDER BY sortid ASC";
		}
		$sql_c = "SELECT count(*) FROM ($sql) as c";
		
		if (!$is_graph) {
			$sql .= " LIMIT $offset,$pagesize";
		}

		$allnum = $this->FetchColOne($sql_c,$parms);
		$allrow = $this->Query($sql,$parms);		
		return array('allrow'=>$allrow,'allnum'=>$allnum);
	}


	/**
	 * 手机品牌、浏览器、系统排行数据
	 * @param [type] $sdate   起始时间
	 * @param [type] $edate   结束时间
	 * @param [type] $station 车站id
	 * @param [type] $type    类型
	 */
	public function MobileRankData($sdate,$edate,$station,$type,$page,$pagesize,$is_graph=0){
		$parms = array($sdate,$edate,$type);
		$offset = ($page-1)*$pagesize;
		if ($station) {
			array_push($parms,$station);
			$sql = "SELECT
						list.num,
						rankname.`name`
					FROM
						(
							SELECT
								`name` AS `CODE`,
								sum(num) AS num
							FROM
								rha_mobile_ranklist
							WHERE
								(
									date BETWEEN ?
									AND ?
								)
							AND type = ?
							AND stationid = ?
							GROUP BY
								`name`
							ORDER BY
								num DESC
						) AS list
					LEFT JOIN rha_mobile_rankname AS rankname ON list.`code` = rankname.`CODE`";
		}else{
			$sql = "SELECT
						list.num,
						rankname.`name`
					FROM
						(
							SELECT
								`name` AS `CODE`,
								sum(num) AS num
							FROM
								rha_mobile_ranklist
							WHERE
								(
									date BETWEEN ?
									AND ?
								)
							AND type = ?
							GROUP BY
								`name`
							ORDER BY
								num DESC
						) AS list
					LEFT JOIN rha_mobile_rankname AS rankname ON list.`code` = rankname.`CODE`";
		}
		$sql_c = "SELECT count(*) FROM ($sql) as c";
		
		
		
		$allnum = $this->FetchColOne($sql_c,$parms);
		$allrows = $this->Query($sql,$parms);
		$rank = 1;
		foreach($allrows as &$v){
			$v['rank'] = $rank;
			$rank++;
		}
		
		$allrow = array_slice($allrows,$offset,$pagesize,true);
		if (!$is_graph) {
			return array('allrow'=>$allrow,'allnum'=>$allnum);
		}
		return array('allrow'=>$allrows,'allnum'=>$allnum);
	}

	/**
	 * 游戏或app下载详情
	 * @param [type] $appid   app或游戏的id
	 * @param [type] $station 车站id
	 * @param [type] $sdate   起始时间
	 * @param [type] $edate   结束时间
	 */
	public function DwonAppInfo($appid,$station,$sdate,$edate)
	{
		$parms = array($sdate,$edate);
		if ($station) {
			array_push($parms,$station);
			array_push($parms,$appid);
			$sql = "SELECT
						date,
						sum(num) as num,
						type,
						stationid,
						appname
					FROM
						rha_downapp
					WHERE
						(date BETWEEN ? AND ? )
						AND stationid = ?
						AND appid = ?
						AND action = 'downapp'
					GROUP BY 
						date 
					ORDER BY
						date ASC";
		}else{
			array_push($parms,$appid);
			$sql = "SELECT
						date,
						sum(num) as num,
						type,
						0 as stationid,
						appname
					FROM
						rha_downapp
					WHERE
						(date BETWEEN ? AND ? )
						AND appid = ?
						AND action = 'downapp'
					GROUP BY 
						date 
					ORDER BY
						date ASC";
		}
		$sql_c = "SELECT count(*) FROM ($sql) as c";
		
		$allnum = $this->FetchColOne($sql_c,$parms);
		$allrow = $this->Query($sql,$parms);
		return array('allrow'=>$allrow,'allnum'=>$allnum);
	}


	/**
	 * 根据下载记录表中的信息获取对应的标题和提示信息
	 */
	public function EventRecordInfo($date,$type,$stationid,$is_all=false)
	{	
		if (!$is_all && !is_array($date)) {

			//单选车站
			if (is_numeric($stationid)) {
				$sql = "SELECT title,descript FROM rha_eventrecord WHERE date = '$date' AND (FIND_IN_SET('$type',type) OR FIND_IN_SET('0',type)) AND (FIND_IN_SET('$stationid',stationid) OR FIND_IN_SET(0,stationid)) AND is_delete = 0";
				$res = $this->Query($sql);
				return $res[0];

			//多选车站
			}else{
				$stationids = explode(',',$stationid);
				foreach($stationids as $val){
					$sql = "SELECT title,descript,date FROM rha_eventrecord WHERE date = '$date' AND (FIND_IN_SET('$type',type) OR FIND_IN_SET('0',type)) AND (FIND_IN_SET('$val',stationid) OR FIND_IN_SET(0,stationid)) AND is_delete = 0";
					$record = $this->Query($sql);
					$records[] = $record[0];
				}

				return $records;
			}
		
		//所有车站	
		}else{
			$sdate = $date[0];
			$edate = $date[1];
			$sql = "SELECT `title`,`descript`,`date` FROM rha_eventrecord WHERE (date BETWEEN '$sdate' AND '$edate') AND is_delete = 0";
			$res = $this->Query($sql);
			return $res;
		}
		
		
	}

	/**
	 * 用户抽奖活动统计
	 */
	public function UserActivity($action,$page,$pagesize,$sdate,$edate,$station,$hour,$type,$is_graph=0){
		$offset = ($page-1)*$pagesize;
		if ($type == 'pv') {
			$num = 'sum(num)';
		}elseif($type == 'uv'){
			$num = 'count(DISTINCT phone)';
		}
		
		if (!$station) {

			$sql = "SELECT $num as $action FROM rha_user_activity WHERE (
									date BETWEEN '$sdate' AND '$edate' )";
			if ($hour) {
				$sql .= " AND `hour` = '$hour'";
			}
			$sql .= "  AND numtype = '$type' AND action = '$action'";
		}else{

			$sql = "SELECT $num as $action FROM rha_user_activity WHERE (
									date BETWEEN '$sdate' AND '$edate' )";
			if ($hour) {
				$sql .= " AND `hour` = '$hour'";
			}

			$sql.= "  AND stationid = $station AND numtype = '$type' AND action = '$action'";
		}
		$sql_c = "SELECT count(*) FROM ($sql) as c";
		
		if (!$is_graph) {
			$sql .= " LIMIT $offset,$pagesize";
		}
		$allnum = $this->FetchColOne($sql_c);
		$allrow = $this->Query($sql);
		return array('allrow'=>$allrow[0][$action],'allnum'=>$allnum);
	}


	/**
	 * 用户抽奖活动页面app下载
	 */
	public function activitydownapp($sdate,$edate,$station,$hour)
	{
		if (!$station) {

			$sql = "SELECT appname,sum(num) as num FROM rha_activity_downapp
					WHERE ( date BETWEEN '$sdate' AND '$edate' ) ";
			if ($hour) {
				$sql .= " AND `hour` = '$hour'";
			}		
			$sql .= " AND action = 'downapp' GROUP BY appid";
			
		}else{

			$sql = "SELECT appname,sum(num) as num FROM rha_activity_downapp
					WHERE ( date BETWEEN '$sdate' AND '$edate' ) ";
			if ($hour) {
				$sql .= " AND `hour` = '$hour'";
			}		
			$sql .= " AND stationid = $station AND action = 'downapp' GROUP BY appid";
		}

		return $this->Query($sql);
	}

	/**
	 * 新用户抽奖活动统计
	 */
	public function NewUserActivity($sdate,$edate,$station,$hour,$type,$action)
	{	
		//wifi、首页、总下载、活动下载、抽奖人数只统计UV
		if ($action == 'sindex_uv' || $action == 'lottery') {
			$type = 'uv';
		}

		if ($type == 'pv') {
			$num = 'sum(num)';
		}elseif($type == 'uv'){
			$num = 'count(DISTINCT phone)';
		}

		if ($action == 'wifi') {

			$sql = "SELECT sum(num) AS num FROM rha_aclog_hour WHERE ( date BETWEEN '$sdate' AND '$edate' ) ";

			if ($station) {				
				if ($hour) {
					$sql .= " AND `hour` = $hour ";
				}
				$sql .= " AND stationid = $station";
			}else{
				if ($hour) {
					$sql .= " AND `hour` = $hour ";
				}
			}
		}elseif($action == 'totaldown' || $action == 'activdown'){
			$sdate=str_replace('-','_',$sdate);
			$edate=str_replace('-','_',$edate);

			$sql = "SELECT COUNT(DISTINCT phone) AS num FROM rha_activity_downapps WHERE ( date BETWEEN '$sdate' AND '$edate' ) ";
			if ($station) {
				
				if ($hour) {
					$sql .= " AND `hour` = $hour ";
				}
				$sql .= " AND stationid = $station";
				if ($action == 'activdown') {
					$sql .= " AND downpage = 'useractivity'";
				}
			}else{

				if ($hour) {
					$sql .= " AND `hour` = $hour ";
				}

				if ($action == 'activdown') {
					$sql .= " AND downpage = 'useractivity'";
				}
			}
			$sql .= " AND action='downapp'";
		}elseif($action == 'sindex_uv'){
			$sdate=str_replace('-','_',$sdate);
			$edate=str_replace('-','_',$edate);
			$sql = "SELECT sum(num) as num FROM rha_user_activitys WHERE (
										date BETWEEN '$sdate' AND '$edate' )";
			if (!$station) {

				if ($hour) {
					$sql .= " AND `hour` = '$hour'";
				}
				$sql .= "  AND numtype = 'uv' AND action = 'sindex_uv'";
			}else{				
				if ($hour) {
					$sql .= " AND `hour` = '$hour'";
				}

				$sql.= "  AND stationid = $station AND numtype = 'uv' AND action = 'sindex_uv'";
			}			
		}else{
			$sdate=str_replace('-','_',$sdate);
			$edate=str_replace('-','_',$edate);
			if ($action == 'activity_window') {
				$action = "activity' AND `from` = 'window";
			}
			if ($action == 'activity_redpacket') {
				$action = "activity' AND `from` = 'redpacket";
			}
			$sql = "SELECT $num as num FROM rha_user_activitys WHERE (
										date BETWEEN '$sdate' AND '$edate' )";
			if (!$station) {

				if ($hour) {
					$sql .= " AND `hour` = '$hour'";
				}
				$sql .= "  AND numtype = '$type' AND action = '$action'";
			}else{				
				if ($hour) {
					$sql .= " AND `hour` = '$hour'";
				}

				$sql.= "  AND stationid = $station AND numtype = '$type' AND action = '$action'";
			}
		}

		$allrow = $this->Query($sql);
		if (!$allrow[0]['num']) {
			$allrow[0]['num'] = 0;
		}
		return $allrow[0]['num'];

	}
}