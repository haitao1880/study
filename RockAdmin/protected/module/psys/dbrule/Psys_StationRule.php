<?php
class Psys_StationRule extends Psys_DbAbstractRule
{
	public function __construct()
	{
		parent::__construct();
	}
	
	//新流程
	public function webCountNew($stationid,$page,$pagesize='15')
	{
		$offset = ($page-1)*$pagesize;
		//连接数
		$sql = "SELECT date,num AS link FROM rha_aclogview WHERE station = $stationid AND date > '2015-01-30' AND date < '".date('Y-m-d',time())."'  ORDER BY date DESC LIMIT $offset,$pagesize";
		$link = $this->Query($sql);	
		$sql = "SELECT
					date,
					sum(ad1) AS ad,
					sum(regpage) AS reg,
					sum(verify) AS verify,
					sum(regnum) AS login,
					sum(ad2) AS wel,
					sum(indexnum) AS sindex,
					sum(down) AS down
				FROM
					(
						SELECT
							date,
							IF(model = 'ad' AND action = 'visit' AND detail = 'uv', dtime, 0 ) AS ad1,

							IF(model = 'register' AND action = 'visit' AND detail = 'uv', dtime, 0 ) AS regpage,
							
							IF(model = 'verify' AND action = 'verify' AND detail = 'verify', dtime, 0) AS verify,

							IF(model = 'register' AND action = 'login' AND detail = 'success', dtime, 0) AS regnum,

							IF(model = 'welcome' AND action = 'visit' AND detail = 'uv', dtime, 0 ) AS ad2,

							IF(model = 'sindex' AND action = 'visit' AND detail = 'uv', dtime, 0 ) AS indexnum,

							IF (action = 'trainDown' AND detail = 'uv', dtime, 0 ) AS down
						FROM
							rha_count_process
						WHERE
							stationid =	$stationid or isnull(stationid)=1 
					) AS a
				GROUP BY date ORDER BY date DESC LIMIT $offset,$pagesize";
		$ad1_down = $this->Query($sql);

		foreach($link as $v_link){
			foreach($ad1_down as &$v_ad1_down){
				$date = str_replace('_','-',$v_ad1_down['date']);
				if ($v_link['date'] == $date) {
					$v_ad1_down['date'] = $v_link['date'];
					$v_ad1_down['link'] = $v_link['link'];
				}
			}
		}		

		return $ad1_down;
	}


	//流程导出
	public function webcountexport($stationid)
	{		
		//连接数
		$sql = "SELECT date,num AS link FROM rha_aclogview WHERE station = $stationid AND date > '2015-01-30' AND date < '".date('Y-m-d',time())."'  ORDER BY date DESC";
		$link = $this->Query($sql);	
		$sql = "SELECT
					date,
					sum(ad1) AS ad,
					sum(regpage) AS reg,					
					sum(regnum) AS login,
					sum(indexnum) AS sindex
				FROM
					(
						SELECT
							date,
							IF(model = 'ad' AND action = 'visit' AND detail = 'uv', dtime, 0 ) AS ad1,

							IF(model = 'register' AND action = 'visit' AND detail = 'uv', dtime, 0 ) AS regpage,

							IF(model = 'register' AND action = 'login' AND detail = 'success', dtime, 0) AS regnum,

							IF(model = 'sindex' AND action = 'visit' AND detail = 'uv', dtime, 0 ) AS indexnum
						FROM
							rha_count_process
						WHERE
							stationid =	$stationid or isnull(stationid)=1 
					) AS a
				GROUP BY date ORDER BY date DESC ";
		$ad1_down = $this->Query($sql);

		foreach($link as $v_link){
			foreach($ad1_down as &$v_ad1_down){
				$date = str_replace('_','-',$v_ad1_down['date']);
				if ($v_link['date'] == $date) {
					$v_ad1_down['date'] = $v_link['date'];
					$v_ad1_down['link'] = $v_link['link'];
				}
			}
		}		

		return $ad1_down;
	}
	//流程统计汇总
	public function webCountAll($page,$pagesize='15')
	{
		$offset = ($page-1)*$pagesize;
		//连接数
		$sql = "SELECT
					date,
					SUM(num) AS link
				FROM
					rha_aclogview
				GROUP BY
					date
				ORDER BY
					date DESC LIMIT $offset,$pagesize";		
		$link = $this->Query($sql);

		$sql = "SELECT
					date,
					sum(ad1) AS ad,
					sum(regpage) AS reg,
					sum(verify) AS verify,
					sum(regnum) AS login,
					sum(ad2) AS wel,
					sum(indexnum) AS sindex,
					sum(down) AS down
				FROM
					(
						SELECT
							date,
							IF(model = 'ad' AND action = 'visit' AND detail = 'uv', dtime, 0 ) AS ad1,

							IF(model = 'register' AND action = 'visit' AND detail = 'uv', dtime, 0 ) AS regpage,

							IF(model = 'verify' AND action = 'verify' AND detail = 'verify', dtime, 0) AS verify,

							IF(model = 'register' AND action = 'login' AND detail = 'success', dtime, 0) AS regnum,

							IF(model = 'welcome' AND action = 'visit' AND detail = 'uv', dtime, 0 ) AS ad2,

							IF(model = 'sindex' AND action = 'visit' AND detail = 'uv', dtime, 0 ) AS indexnum,

							IF (action = 'trainDown' AND detail = 'uv', dtime, 0 ) AS down
						FROM
							rha_count_process
						WHERE
							stationid IN (SELECT id FROM rha_station) or isnull(stationid)=1	
					) AS a
				GROUP BY date ORDER BY date DESC LIMIT $offset,$pagesize";
		$ad1_down = $this->Query($sql);
		$this->SetDb('db-rht_idc');
		// $sql = ""

		foreach($link as $v_link){
			foreach($ad1_down as &$v_ad1_down){
				$date = str_replace('_','-',$v_ad1_down['date']);
				if ($v_link['date'] == $date) {
					$v_ad1_down['date'] = $v_link['date'];
					$v_ad1_down['link'] = $v_link['link'];
				}
			}
		}	
		
		return $ad1_down;
		
	}
	//查询图片目录
	public function dirList($stationid)
	{
		$sql = "SELECT acfile FROM rha_station WHERE id = $stationid";
		$data = $this->Query($sql);
		return $data[0]['acfile'];
	}
	//查询车站列表
	public function stationList()
	{
		$sql = "SELECT id,stationname,acfile FROM rha_station";
		return $this->Query($sql);
	}
	//查询记录总数
	public function totalrows()
	{
		$sql = "SELECT COUNT(DISTINCT date) AS count FROM rha_count";
		$res = $this->Query($sql);
		return $res[0]['count'];

	}
	//新流程查询记录总数
	public function newtotalrows()
	{
		$sql = "SELECT COUNT(DISTINCT date) AS count FROM rha_count where date > '2015_01_30' AND date < '".date('Y_m_d',time())."'";
		$res = $this->Query($sql);
		return $res[0]['count'];
	
	}
	//每日伙伴数据
	public function Everyday($date,$stationid)
	{	
		//每日连接数
		$_date = str_replace('_', '-', $date);
		$sql = "SELECT COUNT(DISTINCT client) AS link FROM rha_aclog WHERE date = '$_date' AND station = $stationid";
		$re = $this->Query($sql);
		//首页、注册、打开、欢迎、提示、下载
		$sql = "SELECT model,action,dtime,sys FROM rha_count WHERE date = '$date' AND stationid = $stationid AND sys in ('ios','android') AND (model = 'index' AND action = 'visit' OR detail = 'regsuccess' OR (model = 'open' AND action = 'first') OR (model = 'welcome' AND action != ''))";
		$list = $this->Query($sql);
		$data['link'] = $re[0]['link'];
		$data['detail'] =$list;
		return $data;			
	}
	//注册统计
	public function RegInfo($page,$pagesize,$stationid)
	{
		$offset = ($page-1)*$pagesize;
		$sql = "SELECT
					date,
					group_concat(detail,IFNULL(sys, ''),'/',dtime) AS postids
				FROM
					rha_count
				WHERE
					action = 'register'
				AND stationid = $stationid
				GROUP BY
				date 
				ORDER BY
				date DESC
				LIMIT $offset,$pagesize";
		$data = $this->Query($sql);
	
		$res['allrow'] = $data;
		return $res;
	}
	//导航点击统计
	public function NavhitInfo($page,$pagesize,$stationid)
	{
		$offset = ($page-1)*$pagesize;
		$sql = "SELECT
					date,
					GROUP_CONCAT(detail, '/', num) AS postids
				FROM
					(
						SELECT
							date,
							detail,
							SUM(dtime) AS num
						FROM
							rha_count
						WHERE
							model = 'nav'
						AND action = 'uv_click'
						AND detail <> 'link'
						AND detail <> 'staiton'
						AND stationid = $stationid
						GROUP BY
							date,
							detail
					) AS nav
				GROUP BY
					date
				ORDER BY
					date DESC
				LIMIT $offset,$pagesize";
		$data = $this->Query($sql);
	
		$res['allrow'] = $data;
		return $res;
	}
	//banner点击统计
	public function PagehitInfo($page,$pagesize,$stationid)
	{
		$offset = ($page-1)*$pagesize;
		$sql = "SELECT
					date,
					GROUP_CONCAT(detail, '/', num) AS postids
				FROM
					(
						SELECT
							date,
							detail,
							SUM(dtime) AS num
						FROM
							rha_count
						WHERE
							model = 'banner'
						AND stationid = $stationid
						GROUP BY
							date,
							detail
					) AS banner
				GROUP BY
					date
				ORDER BY
					date DESC
				LIMIT $offset,$pagesize";
		$data = $this->Query($sql);
	
		$res['allrow'] = $data;
		return $res;
	}
	//影视统计
	public function MovieInfo($page,$pagesize,$stationid)
	{
		$offset = ($page-1)*$pagesize;
		$sql = "SELECT
					date,
					GROUP_CONCAT(detail, '/', num) AS postids
				FROM
					(
						SELECT
							date,
							detail,
							SUM(dtime) AS num
						FROM
							rha_count
						WHERE
							stationid = $stationid
						AND model = 'movie'
						AND action = 'click'
						GROUP BY
							date,
							detail
					) AS movie
				GROUP BY
					date
				ORDER BY
					date DESC
				LIMIT $offset,$pagesize";
		$data = $this->Query($sql);
		$res['allrow'] = $data;
		return $res;
	}
	//影视详情
	function MovieDetail($date,$stationid)
	{
	
		$sql = "SELECT
					v.vname,
					did,
					SUM(dtime) AS num
				FROM
					rha_count 
					JOIN rht_train.rht_video AS v ON did = v.id
				WHERE
					date = '$date'
					AND stationid = '$stationid'
					AND model = 'movie'
					AND action = 'click'
					AND detail = 'play'
				GROUP BY
					did
				ORDER BY
					num DESC";
		$data = $this->Query($sql);
		
		return $data;
	
	}
	//音乐统计
	public function MusicInfo($page,$pagesize,$stationid)
	{
		$offset = ($page-1)*$pagesize;
		$sql = "SELECT
					date,
					GROUP_CONCAT(detail, '/', num) AS postids
				FROM
					(
						SELECT
							date,
							detail,
							SUM(dtime) AS num
						FROM
							rha_count
						WHERE
							stationid = $stationid
						AND model = 'music'
						AND action = 'click'
						GROUP BY
							date,
							detail
						ORDER BY
							date DESC
					) AS music
				GROUP BY
					date
				ORDER BY
					date DESC
				LIMIT $offset,$pagesize";
		$data = $this->Query($sql);
		$res['allrow'] = $data;
		return $res;
	}
	//音乐详情
	function MusicDetail($date,$stationid)
	{
	
		$sql = "SELECT
					m.mname,
					did,
					SUM(dtime) AS num
				FROM
					rha_count
					JOIN rht_train.rht_albummusic AS m ON did = m.musicid
				WHERE
					date = '$date'
				AND stationid = '$stationid'
				AND model = 'music'
				AND action = 'click'
				AND detail = 'play'
				GROUP BY
					did
				ORDER BY	
					num DESC";
		$data = $this->Query($sql);
		return $data;
	
	}
	//应用统计
	public function AppInfo($page,$pagesize,$stationid)
	{
		$offset = ($page-1)*$pagesize;
		$sql = "SELECT
					date,
					GROUP_CONCAT(action, '/', num) AS postids
				FROM
					(
						SELECT
							date,
							action,
							SUM(dtime) AS num
						FROM
							rha_count
						WHERE
							stationid = $stationid
						AND model IN ('app', 'game')
						AND action IN (
							'click',
							'down',
							'downfinish',
							'update'
						)
						GROUP BY
							date,
							action
					) AS app
				GROUP BY
					date
				ORDER BY
					date DESC
				LIMIT $offset,$pagesize";
		$data = $this->Query($sql);
		$res['allrow'] = $data;
		return $res;
	}
	//下载详情
	function DownInfo($date,$stationid)
	{
		$sql = "SELECT
					a.appname,
					did,
					SUM(dtime) AS num
				FROM
					rha_count
					JOIN rht_train.rht_apps AS a ON did = a.id
				WHERE
					date = '$date'
				AND stationid = '$stationid'
				AND action = 'down'
				GROUP BY
					did
				ORDER BY
					num DESC";
		$data = $this->Query($sql);
		return $data;
	
	}
	//web 应用/游戏 统计
	public function webAppInfo($stationid,$select,$page,$pagesize)
	{
		$offset = ($page-1)*$pagesize;
		$sql = "SELECT COUNT(DISTINCT date) as count FROM rha_count_record WHERE stationid = $stationid AND model = 'app' AND (action = 'click_banner' or action = 'alert' or action = 'down')";
		$data['count'] = $this->Query($sql);
		$sql = "SELECT
			date,
			GROUP_CONCAT(action, '/', num) AS postids
			FROM
			(
				SELECT
					date,
					action,
					SUM(dtime) AS num
				FROM
					rha_count_record
				WHERE
					stationid = $stationid
				AND model = '$select'
				AND (
					action = 'click_banner'
					OR action = 'alert'
					OR action = 'down'
				)
				GROUP BY
					date,
					action
			) AS app
		GROUP BY
			date
		ORDER BY
			date DESC
		LIMIT $offset,$pagesize";
		$data['allrows'] = $this->Query($sql);
		return $data;
	}
	//web 游戏、应用详情统计
	function webAppDetail($date,$stationid,$select)
	{
		$sql = "SELECT detail,SUM(dtime) as num FROM rha_count_record WHERE stationid = $stationid AND date = '$date' AND model = '$select' AND action = 'click_banner' GROUP BY detail";
		
		$data['banner'] = $this->Query($sql);
		$sql = "SELECT
			detail,
			GROUP_CONCAT(action, '/', num) AS postids
			FROM
			(
				SELECT
					detail,
					action,
					SUM(dtime) AS num
				FROM
					rha_count_record
				WHERE
					stationid = $stationid
				AND date = '$date'
				AND model = '$select'
				AND (
					action = 'alert'
					OR action = 'down'
				)
				GROUP BY
					detail,
					action
			) AS app
		GROUP BY detail";
		$data['app'] = $this->Query($sql);
		
		return $data;

	}
	
	//order统计
	public function OrderInfo($page,$pagesize,$condition)
	{
		$this->SetDb('db-rht_idc');
		$offset = ($page-1)*$pagesize;
		if($condition['order_number']){
			$order_number = $condition['order_number'];
			$where = " where order_number = $order_number";
		}else{
			if($condition['order_state']){
				$order_state = $condition['order_state'];
				
				if($condition['order_time']){
					$order_time = $condition['order_time'];
					$length = strlen($order_time);
					if($length == 4){
						$where = "where year(order_time) = $order_time";
					}elseif($length == 7){
						$year = substr($order_time,0,4);
						$month = substr($order_time,5);
						$where = "where year(order_time) = $year AND month(order_time) = $month";
					}else{
						$where = "where date(order_time) = '$order_time'";
					}
					if($order_state == 'success'){
						$where .= " AND (state = 'success' or state = 'finish')";
					}else{
						$where .= " AND state = '$order_state'";
					}
				}else{
					if($order_state == 'success'){
						$where = " where state = 'success' or state = 'finish'";
					}else{
						$where = " where state = '$order_state'";
					}
				}
			}
		}
		
		$sql1 = "SELECT order_time,order_number,total_price,go_train_ticket_order_info AS detail,state FROM rht_trainorder $where ORDER BY order_time DESC LIMIT $offset,$pagesize";
		$sql2 = "SELECT count(order_number) as rows FROM rht_trainorder $where";
		$data = $this->Query($sql1);
		$data['rows'] = $this->Query($sql2);
		return $data;
	}
	
	public function AcList($offset,$pagesize = 10,$stationid = 1)
	{
		$sql = "select date,COUNT(DISTINCT client) as num  from rha_aclog where station = $stationid GROUP BY date order by date desc limit $offset,$pagesize";
		$data = $this->Query($sql);
		$count_sql = "select COUNT( DISTINCT date) as allnum from rha_aclog ";
		$res = $this->Query($count_sql);
		$res['allrow'] = $data;
		return $res;
	}
	
	function AcTime($date,$stationid)
	{
		
		$sql = "SELECT
				COUNT(DISTINCT client) as num,
				HOUR (`time`) AS h
				from rha_aclog
				WHERE
				date = '$date' AND station = '$stationid'
				GROUP BY
				h
				ORDER BY h";
		$data = $this->Query($sql);
		return $data;
		
	}
	
	function ApLog($date,$stationid)
	{
		$sql = "SELECT
				COUNT(DISTINCT client) AS num,
				date,
				ap
				FROM
				rha_aclog
				WHERE
				date = '$date' AND station = '$stationid'
				GROUP BY
				date,
				ap
				ORDER BY
				date";
		
		$data = $this->Query($sql);
		return $data;
	}
	
	function ApDetail($date,$stationid)
	{
		$sql = "SELECT
					COUNT(DISTINCT client) AS num,
					ap,
					HOUR(time) as h
				FROM
					rha_aclog
				WHERE
					date = '$date' AND station = '$stationid'
				GROUP BY
					ap,h
				ORDER BY
					ap";
		
		$data = $this->Query($sql);
		return $data;
	}

	/**
	 * 获取监控日志概况
	 */
	public function CheckLogList(){
		$sql_error = "SELECT  cdate AS scdate,station
				FROM
					rha_checklog
				WHERE
					state != 0
				GROUP BY
					scdate,
					station ORDER BY scdate desc";
		$sql_success = "SELECT  cdate AS scdate,station
				FROM
					rha_checklog
				WHERE
					state = 0
				GROUP BY
					scdate,
					station ORDER BY scdate desc";
		$data['error'] = $this->Query($sql_error);
		$data['success'] = $this->Query($sql_success);
		// var_dump($data);
		// exit;
		return $data;
	}



	/**
	 * 用户连接走势
	 * @param int $stationid 车站id
	 * @param date $sdate    起始日期
	 * @param date $edate    结束日期
	 */
	public function UserConnect($stationid,$where_date){
		$sql = "call sp_userconnect(\"$stationid\",\"$where_date\",1,@res)";		
		$data = $this->QueryAll($sql);
		// var_dump($data);
		// exit;
		return $data;

	}

	/**
	 * 用户注册走势
	 * @param int $stationid 车站id
	 * @param date $sdate    起始日期
	 * @param date $edate    结束日期
	 */
	public function UserReg($stationid,$where_date){
		$type_ios = " AND sys = 'ios'";
		$sql_ios = "call sp_userreg(\"$stationid\",\"$where_date\",\"$type_ios\",@res)";

		$type_android = " AND sys = 'android'";
		$sql_android = "call sp_userreg(\"$stationid\",\"$where_date\",\"$type_android\",@res)";

		$type_total = '';
		$sql_total = "call sp_userreg(\"$stationid\",\"$where_date\",\"$type_total\",@res)";

		$data['ios'] = $this->QueryAll($sql_ios);
		$data['android'] = $this->QueryAll($sql_android);
		$data['total'] = $this->QueryAll($sql_total);
		// var_dump($data);
		// exit;
		return $data;
	}

	/**
	 * 用户下载走势
	 * @param int $stationid 车站id
	 * @param date $sdate    起始日期
	 * @param date $edate    结束日期
	 */
	public function UserDown($stationid,$where_date){

		$type_ios = " AND sys = 'ios'";
		$sql_ios = "call sp_userdown(\"$stationid\",\"$where_date\",\"$type_ios\",@res)";
	
		$type_android = " AND sys = 'android'";
		$sql_android = "call sp_userdown(\"$stationid\",\"$where_date\",\"$type_android\",@res)";
		
		$type_total = '';
		$sql_toatal = "call sp_userdown(\"$stationid\",\"$where_date\",\"$type_total\",@res)";
		
		$data['ios'] = $this->QueryAll($sql_ios);
		$data['android'] = $this->QueryAll($sql_android);
		$data['total'] = $this->QueryAll($sql_toatal);
		// var_dump($data);
		// exit;
		return $data;
	}

	/**
	 * 转化率走势
	 * @param int $stationid 车站id
	 * @param date $sdate    起始日期
	 * @param date $edate    结束日期
	 */
	public function UserRate($stationid,$reg_where,$connect_where){
		$type_ios = " AND sys = 'ios'";
		$sql_ios = "call sp_userrate(\"$stationid\",\"$reg_where\",\"$connect_where\",\"$type_ios\",@res)";
		

		$type_android = " AND sys = 'android'";
		$sql_android = "call sp_userrate(\"$stationid\",\"$reg_where\",\"$connect_where\",\"$type_android\",@res)";
		

		$data['ios'] = $this->QueryAll($sql_ios);
		$data['android'] = $this->QueryAll($sql_android);
		// var_dump($data);
		// exit;
		return $data;
	}

	/**
	 * 每日Ios流程步骤流失率
	 * @param int $stationid 车站id
	 * @param date $sdate    起始日期
	 * @param date $edate    结束日期
	 */
	public function UserRose($stationid,$where_date){
		$type_ios = " AND sys = 'ios'";
		$res= $this->GetRegNum($stationid,$where_date,$type_ios);
		$ad1 = $res['ad1'];
		$regpage = $res['regpage'];
		$sql_ios = "call sp_userrose(\"$stationid\",\"$where_date\",\"$type_ios\",\"$ad1 \",\"$regpage \",@res)";
						
		$type_android = " AND sys = 'android'";
		$res= $this->GetRegNum($stationid,$where_date,$type_android);
		$ad1 = $res['ad1'];
		$regpage = $res['regpage'];
		$sql_android = "call sp_userrose(\"$stationid\",\"$where_date\",\"$type_android\",\"$ad1 \",\"$regpage \",@res)";
		
		$type_total = "";
		$res= $this->GetRegNum($stationid,$where_date,$type_total);
		$ad1 = $res['ad1'];
		$regpage = $res['regpage'];
		$sql_total = "call sp_userrose(\"$stationid\",\"$where_date\",\"$type_total\",\"$ad1 \",\"$regpage \",@res)";
		
		$ios = $this->QueryAll($sql_ios);
		$android = $this->QueryAll($sql_android);
		$total = $this->QueryAll($sql_total);

		$data['ios'] = $ios[0];
		$data['android'] = $android[0];
		$data['total'] = $total[0];
		// var_dump($data);
		// exit;
		return $data;
	}

	/**
	 * 每日流程数据概况
	 * @param int $stationid 车站id
	 * @param date $sdate    起始日期
	 * @param date $edate    结束日期
	 */
	public function DailyProcess($stationid,$where_connect,$where_ad1){
		$sql_connect = "call sp_userconnect(\"$stationid\",\"$where_connect\",0,@res)";

		$res = $this->GetRegNum($stationid,$where_ad1,'');
		$ad1 = $res['ad1'];
		$regpage = $res['regpage'];

		$sql_data = "call sp_userprocess(\"$stationid\",\"$where_ad1\",\"$ad1 \",\"$regpage \",@res)";
		
		$connum = $this->FetchColOne($sql_connect);
		$ad1_down = $this->QueryAll($sql_data);
		$data = array_values($ad1_down[0]);
		array_unshift($data,$connum);
		// var_dump($data);
		// exit;
		return $data;
	}

	//上周用户连接数，注册数、下载数情况
	public function PrevWeekTotal($stationid,$reg_where,$connect_where){
		$sql_connect = "call sp_userconnect(\"$stationid\",\"$connect_where\",1,@res)";
		$type_reg = '';
		$sql_reg = "call sp_userreg(\"$stationid\",\"$reg_where\",\"$type_reg\",@res)";

		$sql_down = "call sp_userdown(\"$stationid\",\"$reg_where\",\"$type_reg\",@res)";

		$data['connect'] = $this->QueryAll($sql_connect);
		$data['reg'] = $this->QueryAll($sql_reg);
		$data['down'] = $this->QueryAll($sql_down);
		return $data;
	}

	//本周用户连接数，注册数、下载数情况
	public function CurWeekTotal($stationid,$reg_where,$connect_where){		
		$sql_connect = "call sp_userconnect(\"$stationid\",\"$connect_where\",1,@res)";
		$type_reg = '';
		$sql_reg = "call sp_userreg(\"$stationid\",\"$reg_where\",\"$type_reg\",@res)";

		$sql_down = "call sp_userdown(\"$stationid\",\"$reg_where\",\"$type_reg\",@res)";

		$data['connect'] = $this->QueryAll($sql_connect);
		$data['reg'] = $this->QueryAll($sql_reg);
		$data['down'] = $this->QueryAll($sql_down);
		return $data;
	}

	//两周注册、下载流失率
	public  function TwoWeekRose($stationid,$reg_where,$connect_where){
		$sql_reg = "call sp_regdownrose(\"$stationid\",\"$reg_where\",\"$connect_where\",1,@res)";
		$sql_down = "call sp_regdownrose(\"$stationid\",\"$reg_where\",\"$connect_where\",0,@res)";
		
		$data['regrose'] = $this->QueryAll($sql_reg);
		$data['downrose'] = $this->QueryAll($sql_down);
		return $data;
	}

	//获取到达广告1和注册页数量
	private function GetRegNum($stationid,$where_date,$type){
		if ($type) {
			$sql_ad1 = "SELECT sum(dtime) AS ad1 FROM rha_count 
		WHERE stationid = $stationid $where_date AND model = 'ad' AND action = 'visit' AND detail = 'uv' $type";

		$sql_regpage = "SELECT sum(dtime) AS regpage FROM rha_count 
		WHERE stationid = $stationid $where_date AND model = 'register' AND action = 'visit' AND detail = 'uv' $type";
		}else{
			$sql_ad1 = "SELECT sum(dtime) AS ad1 FROM rha_count 
		WHERE stationid = $stationid $where_date AND model = 'ad' AND action = 'visit' AND detail = 'uv' AND (sys='ios' or sys = 'android')";

		$sql_regpage = "SELECT sum(dtime) AS regpage FROM rha_count 
		WHERE stationid = $stationid $where_date AND model = 'register' AND action = 'visit' AND detail = 'uv' AND (sys='ios' or sys = 'android')";
		}
		
		
		$res['ad1'] = $this->FetchColOne($sql_ad1);
		$res['regpage'] = $this->FetchColOne($sql_regpage);
		return $res;

	}

	//每日所有车站流程汇总数据
  	public function StationInfo($where_connect,$where_process){
  		$sql_connect = "SELECT sum(num) as num  from rha_aclogview where $where_connect";

		$sql_data = "SELECT
						sum(regnum) as regnum,sum(indexnum) as indexnum,sum(down) as down
					FROM(
						SELECT date,
						IF ( model = 'register' AND action = 'login' AND detail = 'success', dtime, 0 ) AS regnum,
						
						IF ( model = 'sindex' AND action = 'visit' AND detail = 'uv', dtime, 0 ) AS indexnum,
						IF ( ( model = 'game' OR model = 'movie' OR model = 'music' OR model = 'sindex' ) AND action = 'trainDown' AND detail = 'uv', dtime, 0 ) AS down
						FROM
							rha_count_record
						WHERE
							stationid IN (SELECT id FROM rha_station) AND $where_process 
						AND (sys = 'android' OR sys = 'ios')
							) AS a";

		$connum = $this->FetchColOne($sql_connect);
		$ad1_down = $this->Query($sql_data)[0];
		
		//获取所有车站广告1和注册页的数量
		$ad1_regpg = $this->GetAd1RegPage($where_process);

		//按照流程格式化数据
		$conn_down = array_values($ad1_down);
		array_unshift($conn_down,$connum,$ad1_regpg['ad1'],$ad1_regpg['regpage']);

		$key = array('conn','ad1','regpage','regnum','indexnum','down');
		$data['keyval'] = array_combine($key,$conn_down);
		$data['val'] = $conn_down;
		// var_dump($data);
		// exit;
		return $data;
  	}

  	//每日每个车站流程汇总数据
  	public function SingleStationInfo($where_connect,$where_process){
  		$sql_connect = "SELECT sum(num) as num,station  from rha_aclogview where $where_connect group by station";
		$sql_data = "SELECT
						station,sum(regnum) as regnum,sum(indexnum) as indexnum,sum(down) as down
					FROM(
						SELECT stationid as station,
						IF ( model = 'register' AND action = 'login' AND detail = 'success', dtime, 0 ) AS regnum,						
						IF ( model = 'sindex' AND action = 'visit' AND detail = 'uv', dtime, 0 ) AS indexnum,
						IF ( ( model = 'game' OR model = 'movie' OR model = 'music' OR model = 'sindex' ) AND action = 'trainDown' AND detail = 'uv', dtime, 0 ) AS down
						FROM
							rha_count_record
						WHERE
							stationid IN (SELECT id FROM rha_station) AND $where_process 
						AND (sys = 'android' OR sys = 'ios')
							) AS a group by station";
		$connum = $this->Query($sql_connect);
		$ad1_down = $this->Query($sql_data);
		
		//获取每个车站广告1和注册页的数量
		$ad1_regpg = $this->GetAd1RegPage($where_process,false);

		//数据组合
		$conn_regpg = array();
		if (!empty($connum)) {
			foreach($connum as $k => $conn){
				foreach($ad1_regpg as $v){
					if ($conn['station'] == $v['station']) {
						$conn_regpg[$k]['station'] = $conn['station'];
						$conn_regpg[$k]['conn'] = $conn['num'];
						$conn_regpg[$k]['ad1'] = $v['ad1'];
						$conn_regpg[$k]['regpage'] = $v['regpage'];
					}
				}
			}
		}else{
			$conn_regpg = $ad1_regpg;
		}
		

		$conn_down = array();
		foreach($conn_regpg as $k1 => $v1){
			foreach($ad1_down as $v2){
				if ($v1['station'] == $v2['station']) {
					$conn_down[$k1]['station'] = $v1['station'];
					$conn_down[$k1]['conn'] = $v1['conn'];
					$conn_down[$k1]['ad1'] = $v1['ad1'];
					$conn_down[$k1]['regpage'] = $v1['regpage'];
					$conn_down[$k1]['regnum'] = $v2['regnum'];
					$conn_down[$k1]['indexnum'] = $v2['indexnum'];
					$conn_down[$k1]['down'] = $v2['down'];
				}
			}
		}

		return $conn_down;
  	}
  	
  
  	/**
  	 * 获取一天所有车站到达广告1，注册页的数量或单个车站到达广告1，注册页的数量
  	 * @param date $where_date 日期条件
  	 * @param bool $type 默认是取出所有车站数据
  	 */
  	private function GetAd1RegPage($where_date,$isall=true){
  		if ($isall) {
  			$sql_ad1 = "SELECT sum(dtime) AS ad1 FROM rha_count 
			WHERE $where_date AND model = 'ad' AND action = 'visit' AND detail = 'uv' AND (sys='ios' or sys = 'android') AND stationid IN (SELECT id FROM rha_station)";

			$sql_regpage = "SELECT sum(dtime) AS regpage FROM rha_count 
			WHERE $where_date AND model = 'register' AND action = 'visit' AND detail = 'uv' AND (sys='ios' or sys = 'android') AND stationid IN (SELECT id FROM rha_station)";

			$res['ad1'] = $this->FetchColOne($sql_ad1);
			$res['regpage'] = $this->FetchColOne($sql_regpage);

  		}else{
  			$sql_ad1 = "SELECT stationid as station,sum(dtime) AS ad1 FROM rha_count 
			WHERE $where_date AND model = 'ad' AND action = 'visit' AND detail = 'uv' AND (sys='ios' or sys = 'android') AND stationid IN (SELECT id FROM rha_station) group by stationid";

			$sql_regpage = "SELECT stationid as station,sum(dtime) AS regpage FROM rha_count 
			WHERE $where_date AND model = 'register' AND action = 'visit' AND detail = 'uv' AND (sys='ios' or sys = 'android') AND stationid IN (SELECT id FROM rha_station) group by stationid";

			$data['ad1'] = $this->Query($sql_ad1);
			$data['regpage'] = $this->Query($sql_regpage);

			//把相同车站的信息合并
			$res = array();
			foreach($data['ad1'] as $k=>$ad1){
				foreach($data['regpage'] as $regpage){
					if ($ad1['station'] == $regpage['station']) {
						$res[$k]['station'] = $ad1['station'];
						$res[$k]['ad1'] = $ad1['ad1'];
						$res[$k]['regpage'] = $regpage['regpage'];
					}
				}
			}
  		}

  		return $res;
  	}


  	/**
	 * wifi每日连接详情数据ajax
	 * @param date $date      日期
	 * @param string $sortname  排序字段
	 * @param string $sortorder 排序方式
	 * @param int $page      当前页
	 * @param int $pagesize  每页条数
	 */
	public function WifiDailyInfo($date,$sortname,$sortorder,$page,$pagesize){
		$offset = ($page-1)*$pagesize;
		$sql = "SELECT * FROM (
					SELECT
						'ALL' AS station,
						SUM(total) AS total,
						SUM(avghour) AS avghour,
						SUM(avgtop) AS avgtop,
						SUM(maxhour) AS maxhour
					FROM
						rha_wifi_daily
					WHERE
						date = '$date'
					UNION
						SELECT
							station,
							total,
							avghour,
							avgtop,
							maxhour
						FROM
							rha_wifi_daily
						WHERE
							date = '$date'
						GROUP BY
							station
					) AS d
				ORDER BY
					$sortname $sortorder";
		$sql_c = "select count(*) from ($sql) as s";
		$sql .= " LIMIT $offset,$pagesize";
		$data['allrow'] = $this->Query($sql);
		$data['allnum'] = $this->FetchColOne($sql_c);
		return $data;
	}

	/**
	 * wifi每日连接详情数据ajax
	 * @param string $sortname  排序字段
	 * @param string $sortorder 排序方式
	 * @param int $page      当前页
	 * @param int $pagesize  每页条数
	 */
	public function WifiWeekInfo($sortname,$sortorder,$page,$pagesize){
		$offset = ($page-1)*$pagesize;
		$sql = "SELECT
					CONCAT(DATE_FORMAT(sdate,'%Y/%m/%d'),'-',DATE_FORMAT(edate,'%Y/%m/%d')) as date,
					sum(totalnum) AS totalnum,
					SUM(avgdaily) AS avgdaily,
					SUM(avghour) AS avghour,
					SUM(avgtop) AS avgtop
				FROM
					rha_wifi_week
				GROUP BY $sortname $sortorder";
		$sql_c = "select count(*) from ($sql) as s";
		$sql .= " LIMIT $offset,$pagesize";
		$data['allrow'] = $this->Query($sql);
		$data['allnum'] = $this->FetchColOne($sql_c);
		return $data;
	}

	/**
	 * 广告1与广告2展示的pv详情
	 * @param int $page          当前页数
	 * @param int $pagesize  
	 * @param date $date      日期
	 * @param str $sortname  排序字段
	 * @param str $sortorder 排序方式
	 */
	public function AdShowPvInfo($page,$pagesize,$date,$sortname,$sortorder){
		$offset = ($page-1)*$pagesize;
		$sql = "SELECT *, (android + ios + other) AS total
				FROM ( SELECT
							date, ad_name, show_type, sum(android) AS android, sum(ios) AS ios, sum(other) AS other
					   FROM ( SELECT * FROM rha_ads_pv WHERE date = '$date') AS a
					   GROUP BY
							show_type,
							ads_id
					 ) AS b
				ORDER BY
					$sortname $sortorder";
		$sql_c = "select count(*) from ($sql) as s";
		$sql .= " LIMIT $offset,$pagesize";
		$data['allrow'] = $this->Query($sql);
		$data['allnum'] = $this->FetchColOne($sql_c);
		return $data;
	}

	/**
	 * 广告pv统计图
	 * @param date $date      
	 * @param int $show_type 广告位
	 */
	public function GraphAdPv($date,$show_type){
		$sql = "SELECT date,ad_name,(android+ios+other) as total 
				FROM(
					SELECT
						date,
						ad_name,
						sum(android) AS android,
						sum(ios) AS ios,
						sum(other) AS other

					FROM
						rha_ads_pv
					WHERE
						date = '$date'
					AND show_type = $show_type
					GROUP BY ad_name
				) as a";

		return $this->Query($sql);
	}

	/**
	 * 获取首页导航点击PV
	 * @param  $sdate     起始时间
	 * @param  $edate     结束时间
	 * @param  $stationid 车站id
	 * @param  $page      当前页数
	 * @param  $pagesize  每页条数
	 * @param  $sortname  排序字段
	 * @param  $sortorder 排序方式
	 */
	public function NavigatorPv($sdate,$edate,$stationid,$page=1,$pagesize=100,$sortname='date',$sortorder='asc'){
		$offset = ($page-1)*$pagesize;
		$sql = "SELECT date,stationid,sum(stationpv) as stationpv,sum(musicpv) as musicpv,sum(gamepv) as gamepv,sum(moviepv) as moviepv,sum(apppv) as apppv
				FROM(
					SELECT 
						date,
						stationid,
						IF(model = 'station',dtime,0) as stationpv,
						IF(model = 'music',dtime,0) as musicpv,
						IF(model = 'game',dtime,0) as gamepv,
						IF(model = 'movie',dtime,0) as moviepv,
						IF(model = 'app',dtime,0) as apppv
					FROM
						rha_count_record
					WHERE
					action = 'visit'
					AND detail = 'pv'
					AND stationid = $stationid
					AND
					(
					 date BETWEEN '$sdate' AND '$edate'
					)
					AND `from` = 2
				) as a 
				group by date";
		$sql_c = "SELECT count(*) FROM ($sql) as c";
		$sql .= " ORDER BY $sortname $sortorder LIMIT $offset,$pagesize";
		$allnum = $this->FetchColOne($sql_c);
		$allrow = $this->Query($sql);
		return array('allrow'=>$allrow,'allnum'=>$allnum);
		
	}

	/**
	 * wifi连接数据
	 * @param  $sdate  起始时间
	 * @param  $edate  截止时间
	 * @param  $station 车站
	 * @return         
	 */
	public function wifidata($sdate,$edate,$station,$page,$pagesize,$is_graph=0){
		$offset = ($page-1)*$pagesize;
		$parms = array($sdate,$edate);

		if ($station) {
			// array_push($parms,$station);
			if (is_numeric($station)) {
				$sql = "SELECT date,num FROM rha_aclogview where date >= ? AND date <= ? AND station = $station ORDER BY date ASC";
			}else{
				$sql = "SELECT date,sum(num) as num FROM rha_aclogview where date >= ? AND date <= ? AND station in($station) GROUP BY date ORDER BY date ASC";
			}
			
		}else{
			$sql = "SELECT date,sum(num) as num FROM rha_aclogview where date >= ? AND date <= ? GROUP BY date ORDER BY date ASC";
		}
		$sql_c = "SELECT count(*) FROM ($sql) as c";
		if (!$is_graph) {
			$sql .= " LIMIT $offset,$pagesize";
		}		
		
		$allnum = $this->FetchColOne($sql_c,$parms);
		$allrow = $this->Query($sql,$parms);
		// echo $this->GetQueryString();
		return array('allrow'=>$allrow,'allnum'=>$allnum);
	}

	/**
	 * 查询当日wifi每小时链接人数
	 * @param  $date    
	 * @param  $station 
	 */
	public function WifiHourNum($date,$station){
		$parms = array($date);
		if ($station) {
			array_push($parms,$station);
			$sql = "SELECT
						`hour`,
						wifinum
					FROM
						rha_wifi_ap
					WHERE
						date = ?
					AND station = ?
					AND wifinum != 0";
		}else{
			$sql = "SELECT
						`hour`,
						sum(wifinum) AS wifinum
					FROM
						rha_wifi_ap
					WHERE
						date = ?
					AND wifinum != 0
					GROUP BY
						`hour`
					ORDER BY
						NULL";
		}

		return $this->Query($sql,$parms);

	}

	/**
	 * 查询当日每个ap访问人数
	 * @param  $date    
	 * @param  $station 
	 */
	public function ApQueryNum($date,$station){
		$parms = array($date);
		if ($station) {
			array_push($parms,$station);
			$sql = "SELECT
						date,
						apkey,
						sum(apnum) AS apnum
					FROM
						rha_wifi_ap
					WHERE
						date = ?
					AND station = ?
					AND wifinum = 0
					GROUP BY
						apkey
					ORDER BY
						NULL";
		}else{
			$sql = "SELECT
						date,
						apkey,
						sum(apnum) AS apnum
					FROM
						rha_wifi_ap
					WHERE
						date = ?
					AND wifinum = 0
					GROUP BY
						apkey
					ORDER BY
						NULL";
		}
		
		return $this->Query($sql,$parms);

	}

	/**
	 * 页面停留时间统计
	 * @param  $sdate  起始时间
	 * @param  $edate  截止时间
	 * @param  $station 车站
	 * @return         
	 */
	public function StayTimeInfo($sdate,$edate,$station,$page,$pagesize){
		$offset = ($page-1)*$pagesize;
		$parms = array($sdate,$edate);

		if ($station) {
			array_push($parms,$station);
			$sql1 = "SELECT
						date,
						model,
 						IF (model = 'webindexindex',dtime, 0) AS indexindex,
						IF (model = 'webindexregister',dtime, 0) AS indexregister,
						IF (model = 'webindexwelcome',dtime, 0) AS indexwelcome,
						IF (model = 'webindexsindex',dtime, 0) AS indexsindex,
						IF (model = 'webstation',dtime, 0) AS stationindex,
						IF (model = 'webmovieindex',dtime, 0) AS movieindex,
						IF (model = 'webmusicindex',dtime, 0) AS musicindex,
						IF (model = 'webgameindex',dtime,0) AS gameindex,
						IF (model = 'webappindex',dtime,0) AS appindex
					FROM
						rha_count_record
					WHERE
						(date BETWEEN ? AND ? )
						AND action = 'stay'
						AND detail = 'timecore'
						AND stationid = ?
						AND `from` = 2";
		}else{
			$sql1 = "SELECT
						date,
						model,
 						IF (model = 'webindexindex',dtime, 0) AS indexindex,
						IF (model = 'webindexregister',dtime, 0) AS indexregister,
						IF (model = 'webindexwelcome',dtime, 0) AS indexwelcome,
						IF (model = 'webindexsindex',dtime, 0) AS indexsindex,
						IF (model = 'webstation',dtime, 0) AS stationindex,
						IF (model = 'webmovieindex',dtime, 0) AS movieindex,
						IF (model = 'webmusicindex',dtime, 0) AS musicindex,
						IF (model = 'webgameindex',dtime,0) AS gameindex,
						IF (model = 'webappindex',dtime,0) AS appindex
					FROM
						rha_count_record
					WHERE
						(date BETWEEN ? AND ? )
						AND action = 'stay'
						AND detail = 'timecore'
						AND `from` = 2";
		}

		$sql = "SELECT
					date,
					sum(indexindex) AS indexindex,
					sum(indexregister) AS indexregister,
					sum(indexwelcome) AS indexwelcome,
					sum(indexsindex) AS indexsindex,
					sum(stationindex) AS stationindex,
					sum(movieindex) AS movieindex,
					sum(musicindex) AS musicindex,
					sum(gameindex) AS gameindex,
					sum(appindex) AS appindex
				FROM ($sql1) as a
				GROUP BY
				date";

		$sql_c = "SELECT count(*) FROM ($sql) as c";
		if (!$is_graph) {
			$sql .= " LIMIT $offset,$pagesize";
		}
		
		$allnum = $this->FetchColOne($sql_c,$parms);
		$allrow = $this->Query($sql,$parms);
		return array('allrow'=>$allrow,'allnum'=>$allnum);
	}

	/**
	 * 广告PV展示
	 * @param  $sdate    
	 * @param  $edate    
	 * @param  $station  
	 * @param  $adtype   广告位置
	 * @param  $page     
	 * @param  $pagesize 
	 */
	public function ShowAdPvInfo($sdate,$edate,$station,$adtype,$page,$pagesize,$is_graph=0){
		$parms = array($sdate,$edate,$adtype);
		$offset = ($page-1)*$pagesize;
		if ($station) {
			array_push($parms,$station);
			$sql = "SELECT
						date,
						detail,
						sum(dtime) as num
					FROM
						rha_count_record
					WHERE
						(date BETWEEN ? AND ?)
						AND model = ?
						AND action = 'show'
						AND stationid = ?
						AND `from` = 2
						AND detail != ''
					GROUP BY date,detail
					ORDER BY date asc";
		}else{
			$sql = "SELECT
						date,
						detail,
						sum(dtime) as num
					FROM
						rha_count_record
					WHERE
						(date BETWEEN ? AND ?)
						AND model = ?
						AND action = 'show'
						AND `from` = 2
						AND detail != ''
					GROUP BY date,detail
					ORDER BY date asc";
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
	 * 每7天注册数据
	 * @param [type] $sdate   
	 * @param [type] $edate   
	 * @param [type] $station 
	 */
	public function RegisterWeekData($sdate,$edate,$station){		
		$sql = "SELECT
					CONCAT(sdate, '/', edate) as datearea,
					sum(totalnum) AS num
				FROM
					rha_register_week 
				";
		if ($sdate && $edate) {
			$sdate = str_replace('-','_',$sdate);
			$edate = str_replace('-','_',$edate);
			$sql .= " WHERE sdate >= '$sdate' AND edate <= '$edate' ";
			if ($station) {
				$sql .= " AND stationid = $station ";
			}
		}else{
			if ($station) {
				$sql .= " WHERE stationid = $station ";
			}
		}	
		
		$sql .= " GROUP BY datearea";
		return $this->Query($sql);
	}

	public function TotalRegNum(){
		$edate = date('Y_m_d',strtotime('-1 day'));
		$sql_total = "SELECT SUM(dtime) AS reg FROM rha_count_process WHERE (date BETWEEN '2015_01_30' AND '$edate') AND model = 'register' AND action = 'login' AND detail = 'success'";
		
		$sql_new = "SELECT SUM(dtime) AS reg FROM rha_count_process WHERE date = '$edate' AND model = 'register' AND action = 'login' AND detail = 'success'";
		$total = $this->FetchColOne($sql_total);
		$new = $this->FetchColOne($sql_new);
		return array('total'=>$total,'new'=>$new);
	}


	/**
	 * 每日注册统计图数据
	 * @param date $sdate  
	 * @param date $edate  
	 * @param int $station 
	 */
	public function RegData($sdate,$edate,$station){
		$parms =  array($sdate,$edate);
		if ($station) {
			// array_push($parms,$station);
			if (is_numeric($station)) {
				$sql = "SELECT date,sum(dtime) as num FROM rha_count_process WHERE (date BETWEEN ? AND ?) AND stationid=$station AND model = 'register' AND action = 'login' AND detail = 'success' GROUP BY date ORDER BY date ASC";
			}else{
				$sql = "SELECT date,sum(dtime) as num FROM rha_count_process WHERE (date BETWEEN ? AND ?) AND stationid in ($station) AND model = 'register' AND action = 'login' AND detail = 'success' GROUP BY date ORDER BY date ASC";
			}
			
		}else{
			$sql = "SELECT date,sum(dtime) as num FROM rha_count_process WHERE (date BETWEEN ? AND ?) AND model = 'register' AND action = 'login' AND detail = 'success' GROUP BY date ORDER BY date ASC";
		}

		return $this->Query($sql,$parms);
		
	}


	/**
	 * 获取活跃用户信息
	 */
	public function UserActiveInfo($station){
		$sql = "SELECT sum(cur_month) AS cur_month,sum(one_month) AS one_month,sum(two_month) AS two_month,sum(three_month) AS three_month,sum(two_befor_month) AS two_befor_month,sum(three_befor_month) AS three_befor_month,sum(cur_month_regnum) AS cur_month_regnum,sum(cur_month_active_user) AS cur_month_active_user FROM rha_active_user WHERE stationid IN ($station) GROUP BY cur_month";
		
		return $this->Query($sql);
	}

	/**
	 * 所选时间段范围内每7天的wifi连接数与注册人数
	 * @param  [type] $station [description]
	 * @param  [type] $sdate   [description]
	 * @param  [type] $edate   [description]
	 * @return [type]          [description]
	 */
	public function wifidataweek($station,$sdate,$edate){
		$ssdate = date('Y/m/d',strtotime($sdate));
		$eedate = date('Y/m/d',strtotime($edate));

		$sssdate = date('Y_m_d',strtotime($sdate));
		$eeedate = date('Y_m_d',strtotime($edate));

		$sql_wifi = "SELECT
						SUM(num) AS wifi
					FROM
						rha_aclogview
					WHERE
						(
							date BETWEEN ?
							AND ?
						)
					AND station IN (?) ";
		$wifi = $this->FetchRow($sql_wifi,array($sdate,$edate,$station));

		$sql_reg = "SELECT
						SUM(dtime) AS reg
					FROM
						rha_count_process 
					WHERE
						(
							date BETWEEN ?
							AND ?
						)
					AND stationid in(?)
					AND model='register'
			        AND action='login'
					AND detail='success'";
		
		$reg = $this->FetchRow($sql_reg,array($sssdate,$eeedate,$station));

		return array('datearea'=>$ssdate.'-'.$eedate,'wifi'=>$wifi['wifi'],'reg'=>$reg['reg']);
	}
	
}