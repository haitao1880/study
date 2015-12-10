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
	
	protected $CurMonthUser = array(); //当前的注册用户

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
	 * 获取当月的注册人数
	 */
	protected function GetCurMonthRegisterNum(){
		$this->cur_month_regnum = $this->UserRegisterNum($this->cur_smonth,$this->cur_edate);
		$this->CurMonthUser = $this->UserRegisterNum($this->cur_smonth,$this->cur_edate,1);
		
	}

	/**
	 * 当月的活跃人数
	 */
	protected function CurActiveUserNum(){
		$this->cur_month_active_user = $this->ActiveUserNum($this->cur_smonth,$this->cur_edate);
	}

	/**
	 * 上月
	 */
	protected function GetOneMonth(){		
		$OneMonth = $this->UserRegisterNum($this->one_smonth,$this->one_emonth,1);			
		$this->one_month_active_user = $this->BackUserNum($OneMonth,$this->CurMonthUser);
		// echo $this->one_month_active_user;
	}

	/**
	 * 前两月
	 */
	protected function GetTwoMonth(){
		$TwoMonth = $this->UserRegisterNum($this->two_smonth,$this->one_emonth,1);	
		$this->two_month_active_user = $this->BackUserNum($TwoMonth,$this->CurMonthUser);
	}

	/**
	 * 前三月
	 */
	protected function GetThreeMonth(){		

		$ThreeMonth = $this->UserRegisterNum($this->three_smonth,$this->one_emonth,1);	
		$this->three_month_active_user = $this->BackUserNum($ThreeMonth,$this->CurMonthUser);
	}

	/**
	 * 上上月活跃用户
	 */
	protected function GetTwoBeforMonth(){		

		$TwoBeforMonth = $this->UserRegisterNum($this->two_smonth,$this->two_emonth,1);	
		$this->two_befor_month_active_user = $this->BackUserNum($TwoBeforMonth,$this->CurMonthUser);
	}


	/**
	 * 上上上月活跃用户
	 */
	protected function GetThreeBeforMonth(){		

		$ThreeBeforMonth = $this->UserRegisterNum($this->three_smonth,$this->three_emonth,1);	
		$this->three_befor_month_active_user = $this->BackUserNum($ThreeBeforMonth,$this->CurMonthUser);
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
						one_month = $this->one_month_active_user,
						two_month = $this->two_month_active_user,
						three_month = $this->three_month_active_user,
						two_befor_month = $this->two_befor_month_active_user,
						three_befor_month = $this->three_befor_month_active_user,
						cur_month_regnum = $this->cur_month_regnum,
						cur_month_active_user = $this->cur_month_active_user
					WHERE
						cur_month = '$this->cur_month'
					AND stationid = $this->stationid";
			$res = $this->db->query($sql);

		}
		var_dump($res).PHP_EOL;
	}  


	/**
	 * 某段时间内的活跃用户
	 */
	protected function ActiveUserNum($smonth,$emonth,$isarray=0){
		$sql = "SELECT					
					COUNT(username) AS num
				FROM
					rha_login_record
				WHERE
					(logintime BETWEEN '$smonth'
				AND '$emonth') AND stationid ='$this->stationid'
				GROUP BY					
					username 
				HAVING num >= 2";

		$res = $this->GetList($sql);		
		
		return count($res);
		
	}
	

	/**
	 * 获取某段时间内的注册用户
	 */
	protected function UserRegisterNum($smonth,$emonth,$isarray=0){
		$sql = "SELECT
					username
				FROM
					rha_login_record
				WHERE
					(logintime BETWEEN '$smonth' AND '$emonth')
				AND stationid = '$this->stationid'";
					
		$res = $this->GetList($sql);
		$temp = array();
		foreach($res as $username){
			$temp[]=$username['username'];
		}

		$temp1 = array_flip(array_flip($temp));
		if (!$isarray) {
			return count($temp1);
		}
		return $temp1;
	}

	/**
	 * 某段时间的注册用户回到本月的用户
	 */
	protected function BackUserNum(array $befor,array $cur){

		$backnum = 0;
		$cur1 = array_flip($cur);
		foreach($befor as $v){
			if (isset($cur1[$v])) {
				$backnum += 1;
			}

		}
		return $backnum;

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
	 * 执行
	 */
	public function executeall($date){
		$this->GetAllDate($date);

		$this->GetCurMonthRegisterNum();
		$this->CurActiveUserNum();

		$this->GetOneMonth();
		$this->GetTwoMonth();
		$this->GetThreeMonth();

		$this->GetTwoBeforMonth();
		$this->GetThreeBeforMonth();

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
// $stationid = 8;
if (!$stationid) {
	exit;
}
$date = date('Y-m-d',strtotime('-1 day'));
$m = new ActiveUser($stationid);
$m -> executeall($date);

/*$sdate = strtotime('2015-02-28');
$sdate = strtotime('+1 month',$sdate);
$sdate = strtotime('-1 day',$sdate);
$date = date('Y-m-d',$sdate);
echo $date;exit;*/
/*function ExecuteDateArea($stationid,$sdate,$edate){
	$sdate = strtotime($sdate);
	$edate = strtotime($edate);
	while ($sdate <= $edate) {
		$m = new ActiveUser($stationid);
		$date = date('Y-m-d',$sdate);
		$m -> executeall($date);		
		// $sdate = strtotime('+1 month',$sdate);
		$sdate = strtotime('+1 day',$sdate);
	}
}*/

/*function ExecuteDateArea($stationid,$sdate,$edate){
	$sdate = strtotime($sdate);
	$edate = strtotime($edate);
	while ($sdate <= $edate) {
		$m = new ActiveUser($stationid);
		$date = date('Y-m-d',$sdate);
		echo $date.PHP_EOL;
		$m -> executeall($date);	
		$sdate = strtotime('+30 day',$sdate);
		
		$year = date('Y',$sdate);
		$month = date('m',$sdate);
		$day = date('d',$sdate);		
		$sdate = mktime(0,0,0,$month+1,1,$year)-1;

	}
}

ExecuteDateArea($stationid,'2015-10-30','2015-12-12');*/


