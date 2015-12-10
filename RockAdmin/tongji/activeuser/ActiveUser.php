<?php 
/**
 * 分析用户登录记录 * 
 */
header("Content-type: text/html; charset=utf-8");
class ActiveUser{
	protected $dsn_content = "mysql:host=localhost;dbname=rht_admin";
	//protected $dsn_content = "mysql:host=192.168.1.201;dbname=rht_admin";
	protected $dbuser_content = 'root';
	protected $dbpasswd_content = 'Cahw_1MLLqIt';
	protected $db ;

	//当天所有的注册用户
	protected $loginlog = array();
	protected $first_users = array(); //当前月第一次出现的用户

	protected $cur_sdate; //当前日期
	protected $cur_edate; //当前日期

	protected $cur_month; //当前月份

	//当前月的起始结束时间
	protected $cur_smonth;
	protected $cur_emonth;

	//上月的起始结束时间
	protected $one_smonth;
	protected $one_emonth;

	//上上月的起始结束时间
	protected $two_smonth;
	protected $two_emonth;

	//上上上月的起始结束时间
	protected $three_smonth;
	protected $three_emonth;

	//当月的注册人数
	protected $cur_month_regnum = 0;	
	protected $cur_month_active_user = 0;//当月的活跃人数

	protected $one_month_active_user = 0;
	protected $two_month_active_user = 0;
	protected $three_month_active_user = 0;
	protected $two_befor_month_active_user = 0;
	protected $three_befor_month_active_user = 0;
	
	public function __construct($stationid){
		$this->db = $this->getdb_content();	
		$this->stationid = $stationid;
		$this->loginlog = array();
		$this->first_users = array();
	}


	/**
	 * 日期推断
	 */
	protected function GetAllDate($date){
		$date = strtotime($date);
		$cur_year = date('Y',$date);
		$cur_month = date('m',$date);
		$cur_day = date('d',$date);

		$this->cur_sdate = mktime(0,0,0,$cur_month,$cur_day,$cur_year);
		$this->cur_edate = mktime(0,0,0,$cur_month,$cur_day+1,$cur_year) -1;

		$this->cur_smonth = mktime(0,0,0,$cur_month,1,$cur_year);
		$this->cur_emonth = mktime(0,0,0,$cur_month+1,1,$cur_year) - 1;

		$this->one_smonth = mktime(0,0,0,$cur_month-1,1,$cur_year);
		$this->one_emonth = mktime(0,0,0,$cur_month,1,$cur_year) - 1;

		$this->two_smonth = mktime(0,0,0,$cur_month-2,1,$cur_year);
		$this->two_emonth = mktime(0,0,0,$cur_month-1,1,$cur_year) - 1;

		$this->three_smonth = mktime(0,0,0,$cur_month-3,1,$cur_year);
		$this->three_emonth = mktime(0,0,0,$cur_month-2,1,$cur_year) - 1;
		
		$this->cur_month = date('Ym',$date);
		
	}

	/**
	 * 获取每天的用户登录记录
	 */
	protected function GetLoginlog(){

		$sql ="SELECT
					username
				FROM
					rha_login_record
				WHERE
					(logintime BETWEEN $this->cur_sdate AND $this->cur_edate) AND stationid = '$this->stationid' ";
		$res = $this->GetList($sql);
		if (!empty($res)) {
			$this->loginlog = $res;
		}
		
	} 


	/**
	 * 筛选出本月第一次出现的记录
	 */
	protected function GetFirstRecord(){
		if (!empty($this->loginlog)) {
			foreach($this->loginlog as $val){
				
				$is_exists = $this->is_exists($val['username'],$this->cur_smonth,$this->cur_emonth);

				//如果不存在则表示该用户在当前月第一次出现
				if (!$is_exists) {
					$this->cur_month_regnum += 1;
					$this->first_users[] = $val['username'];					

				}else{
					$iscuractive = $this->IsCurMonthActiveUser($val['username']);
					if (!$iscuractive) {
						$this->cur_month_active_user += 1;
						$data = array('month'=>$this->cur_month,'username'=>$val['username'],'stationid'=>$this->stationid);
						$this->Insert($data,'rha_useractive_record');
					}				
					
				}
			}
		}
		

	}

	/**
	 * 如果本月第一次出现的记录,在上月出现过，则来到本月的上月活跃用户+1
	 */
	protected function GetOneMonth(){
		if (empty($this->first_users)) {
			return;
		}
		foreach($this->first_users as $v){
			$is_exists = $this->is_exists($v,$this->one_smonth,$this->one_emonth);
			if ($is_exists) {
				$this->one_month_active_user += 1;
			}
		}

	}

	/**
	 * 如果本月第一次出现的记录，在前两月中出现过，则本月的两月活跃用户+1
	 */
	protected function GetTwoMonth(){
		if (empty($this->first_users)) {
			return;
		}
		foreach($this->first_users as $v){
			$is_exists = $this->is_exists($v,$this->two_smonth,$this->one_emonth);
			if ($is_exists) {
				$this->two_month_active_user += 1;
			}
		}
	}

	/**
	 * 如果本月第一次出现的记录，在前三月中出现过，则本月的三月活跃用户+1
	 */
	protected function GetThreeMonth(){
		if (empty($this->first_users)) {
			return;
		}

		foreach($this->first_users as $v){
			$is_exists = $this->is_exists($v,$this->three_smonth,$this->one_emonth);
			if ($is_exists) {
				$this->three_month_active_user += 1;
			}
		}
	}

	/**
	 * 如果本月第一次出现的记录，在上上月中出现过，则本月的上上月活跃用户+1
	 */
	protected function GetTwoBeforMonth(){

		if (empty($this->first_users)) {
			return;
		}

		foreach($this->first_users as $v){
			$is_exists = $this->is_exists($v,$this->two_smonth,$this->two_emonth);
			if ($is_exists) {
				$this->two_befor_month_active_user += 1;
			}
		}
	}


	/**
	 * 如果本月第一次出现的记录，在上上上月中出现过，则本月的上上上月活跃用户+1
	 */
	protected function GetThreeBeforMonth(){
		if (empty($this->first_users)) {
			return;
		}
		foreach($this->first_users as $v){
			$is_exists = $this->is_exists($v,$this->three_smonth,$this->three_emonth);
			if ($is_exists) {
				$this->three_befor_month_active_user += 1;
			}
		}
	}

	/**
	 * 入库操作
	 */
	protected function intotable(){
		//如果已经存在本月的记录，则在原有的基础上加当天的，如果不存在本月的数据，则直接插入
		$is_exists = $this->is_exists_active();
		if (!$is_exists) {
			$data = array(
				'cur_month'=>$this->cur_month,
				'one_month'=>$this->one_month_active_user,
				'two_month' => $this->two_month_active_user,
				'three_month' => $this->three_month_active_user,
				'two_befor_month' => $this->two_befor_month_active_user,
				'three_befor_month' => $this->three_befor_month_active_user,
				'cur_month_regnum' => $this->cur_month_regnum,
				'cur_month_active_user' => $this->cur_month_active_user,
				'stationid' => $this->stationid
			);
			$res = $this->Insert($data,'rha_active_user');			
		}else{
			$sql = "UPDATE 
					 	rha_active_user
					SET 
						one_month = one_month + $this->one_month_active_user,
						two_month = two_month + $this->two_month_active_user,
						three_month = three_month + $this->three_month_active_user,
						two_befor_month = two_befor_month + $this->two_befor_month_active_user,
						three_befor_month = three_befor_month + $this->three_befor_month_active_user,
						cur_month_regnum = cur_month_regnum + $this->cur_month_regnum,
						cur_month_active_user = cur_month_active_user + $this->cur_month_active_user
					WHERE
						cur_month = '$this->cur_month'
					AND stationid = $this->stationid";
			$res = $this->db->query($sql);

		}
		var_dump($res).PHP_EOL;
	}

	/**
	 * 判断记录是否已经存在
	 */
	protected function is_exists_active(){
		$sql = "SELECT count(*) as num FROM rha_active_user WHERE cur_month='$this->cur_month' AND stationid='$this->stationid' ";		
		$res = $this->FetRow($sql);
		return $res['num'];
	}
	

	/**
	 * 判断记录在时间段内是否第一次出现
	 * @return $month 
	 */
	protected function is_exists($username,$smonth,$emonth){	

		$sql = "SELECT count(*) AS num FROM rha_login_record WHERE (logintime BETWEEN '$smonth' AND '$emonth') AND logintime < '$this->cur_sdate' AND username = '$username' AND stationid='$this->stationid'"; 
		$res = $this->FetRow($sql);
		return $res['num'];
		
 	}

 	/**
 	 * 判断本月的活跃用户
 	 */
 	public function IsCurMonthActiveUser($username){
 		$sql = "SELECT count(*) as num FROM rha_useractive_record WHERE cur_month='$this->cur_month' AND stationid='$this->stationid' AND username='$username'";		
		$res = $this->FetRow($sql);
		return $res['num'];
 	}

 	/**
	 * 执行
	 */
	public function executeall($date){
		$this->GetAllDate($date);
		$this->GetLoginlog();
		$this->GetFirstRecord();

		/*$this->GetOneMonth();
		$this->GetTwoMonth();
		$this->GetThreeMonth();

		$this->GetTwoBeforMonth();
		$this->GetThreeBeforMonth();*/

		$this->intotable();
		
	}
	
	
	
	/**
	 * 连接数据库
	 * 
	 */
	protected function getdb_content(){
		if ($this->db === null) {
			$db = new PDO($this->dsn_content, $this->dbuser_content, $this->dbpasswd_content);
			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$db->exec('set names utf8');
		
			return $db;
		}else{
			return $this->db;
		}
		
	}

	/**
	 * 执行sql语句
	 * @param string $sql  sql语句
	 * @param array $data 插入的值
	 */
	protected function Execute($sql){
		$dbd = $this->db->prepare($sql);
		$dbd->execute();
	}

	

	/**
	 * 插入数据库
	 * @param array $data 
	 * @param string $tb
	 * @return num : >0 插入成功
	 */
	protected function Insert($data,$tb='rha_count')
	{	
		$key = '';
		$var = '';
		foreach ($data as $kt=>$vt)
		{
			$key.= "`$kt`,";
			$var.= "?,";
		}
		$key = rtrim($key,',');
		$var = rtrim($var,',');
		$in_sql = "Insert Into `$tb`($key) Value ($var);";
		$sth = $this->db->prepare($in_sql);
		$sth->execute(array_values($data));
		return $this->db->lastInsertId();
		
	}

	/**
	 * 返回所有数据
	 * @param 
	 */
	protected function GetList($sql)
    {	
		$p = $this->db->query($sql);
		$p->setFetchMode(PDO::FETCH_ASSOC);
		$result = $p->fetchAll();
        return $result;
    }

    /**
	 * 返回一行数据
	 * @param 
	 */
	protected function FetRow($sql)
    {	
		$p = $this->db->query($sql);
		$p->setFetchMode(PDO::FETCH_ASSOC);
		$result = $p->fetchAll();
        return $result[0];
    }

}



error_reporting(E_ALL^E_NOTICE);
//接收cid
$stationid = $argv[1];
if (!$stationid) {
	exit;
}
/*$date = date('Y-m-d',strtotime('-1 day'));
$m = new ActiveUser($stationid);
$m -> executeall($date);*/

function ExecuteDateArea($stationid,$sdate,$edate){
	$sdate = strtotime($sdate);
	$edate = strtotime($edate);
	while ($sdate <= $edate) {
		$m = new ActiveUser($stationid);
		$date = date('Y-m-d',$sdate);
		$m -> executeall($date);		
		$sdate = strtotime('+1 day',$sdate);
	}
}

ExecuteDateArea($stationid,'2015-02-01','2015-11-11');


