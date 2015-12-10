<?php
/**
 * 连接wifi新旧用户统计
 */
require '/home/upload/nginxlog/database/Database.php';
class IsNewUserMac
{
	protected $date;
	protected $newuser = 0;
	protected $olduser = 0;

	protected $mac; //当天不重复的mac
	protected $stationid;

	protected $db;
	protected $stations;
	protected $table = 'rha_aclog_record';


	public function __construct()
	{					
		$this->db = Database::GetIns();	
		$this->table = $this->table.'_'.date('Y_m');
		$sql = "Select `id` as `stationid`,`acfile` as `name`,`ap`,`acip`,`is_alone` From `rha_station`";
		$this->stations = $this->db->FetAll($sql);
		// $this->stations = array(7,8,9,10,11,12,13,14,15);
		
		foreach($this->stations as $v){
			if ($v == 16) {
				continue;
			}
			//$this->execsql('2015-08-01','2015-08-14',$v['stationid']);
			$this->ExcuteAll($v['stationid']);	
		}	
				
	}

	//根据当前的日期查询出不重复的mac
	protected function GetUniqueMac()
	{	 
		$this->mac = array();
		$sql = "SELECT client from $this->table WHERE date = '$this->date' AND  stationid = $this->stationid GROUP BY client";
		$this->mac = $this->db->FetAll($sql);
	}

	//判断是新用户还是老用户
	protected function IsOldUser()
	{			
		foreach($this->mac as $v){
			$where = array('client'=>$v['client'],'stationid'=>$this->stationid);		
			$res = $this->db->IsExists($where,'rha_all_user_mac');
			if (!$res) {
				$data = array('client'=>$v['client'],'date'=>$this->date,'stationid'=>$this->stationid);
				$this->db->Insert($data,'rha_all_user_mac');
				$this->newuser += 1;
			}else{
				$this->olduser += 1;
			}
		}
	}

	//添加数据到新旧用户数量表rha_traffic_daily
	protected function Traffic()
	{
		$data = array('date'=>$this->date,
					  'newuser'=>$this->newuser,
					  'olduser'=>$this->olduser,
					  'stationid'=>$this->stationid
				);
		$this->db->Insert($data,'rha_traffic_daily');
	}

	//执行所有流程
	protected function ExcuteAll($stationid,$date='')
	{	
		$this->newuser = 0;		
		$this->olduser = 0;
		$this->stationid = $stationid;
		if (!$date) {
			$this->date = date('Y-m-d',strtotime('-1 day'));
		}else{
			$this->date = $date;
		}		

		$this->GetUniqueMac();
		$this->IsOldUser();
		$this->Traffic();
	}

	//获取多日数据
	protected function execsql($sdate,$edate,$stationid)
	{
		$sdate = strtotime($sdate);
		$edate = strtotime($edate);
		while ($sdate <= $edate) {
			
			$date = date('Y-m-d',$sdate);
			$this->ExcuteAll($stationid,$date);
			$sdate = strtotime('+1 day',$sdate);
		}
	}
}

error_reporting(E_ALL^E_NOTICE);

$obj = new IsNewUserMac();


