<?php
/**
 * 伙伴首次激活统计脚本
 * tongjigame.wonaonao.com:81/record.php?open/pv/android/d4:97:0b:96:c5:91/1.4.6
 */
header("Content-type: text/html; charset=utf-8");
ini_set('date.timezone', 'Asia/Shanghai');
ini_set('date.default_latitude', 31.5167);
ini_set('date.default_longitude', 121.4500);
$curdir = dirname(__FILE__);
require_once $curdir . DIRECTORY_SEPARATOR . 'record_query.php';

class OpenTrianApp
{
	protected $db;
	protected $newuser = 0;
	protected $olduser = 0;
	protected $date;
	protected $hour;
	public function __construct($connect)
	{
		$this->db = new queryModel($connect);
		$this->SetTb();
		$this->date = date('Y-m-d');
		$this->hour = date('H');
	}

	//设置表
	protected function SetTb($tb='rha_open_trainapp')
	{
		$this->db->setTable($tb);
	}

	//判断打开的用户是新用户还是老用户
	protected function IsOldUserOpen($usermac)
	{
		$where = " usermac='$usermac'";
		return $this->db ->CountNum($where);
	}

	//判断是否存在当前时间的记录
	protected function IsExistsRecord()
	{	$this->db->setTable('rha_open_trainapp_usernum');
		$where = " date = '$this->date' and hour = '$this->hour' ";
		return $this->db ->CountNum($where);
	}

	//添加或修改当前时间段的记录
	protected function AddOrUpdate($sys,$version)
	{
		$isexists = $this->IsExistsRecord();
		if ($isexists) {
			$sql = " UPDATE rha_open_trainapp_usernum SET newuser = newuser + $this->newuser,olduser = olduser + $this->olduser WHERE date = '$this->date' AND hour = '$this->hour' AND sys='$sys'";
			$this->db->query($sql);
		}else{
			$data['date'] = $this->date;
			$data['hour'] = $this->hour;
			$data['newuser'] = $this->newuser;
			$data['olduser'] = $this->olduser;
			$data['sys'] = $sys;
			$data['version'] = $version;
			$this->db->AddOne($data);
		}
	}

	//信息入库或修改
	public function IntoDatabase($usermac,$sys,$type,$version)
	{
		$isold = $this->IsOldUserOpen($usermac);
		if ($isold) {
			$this->olduser += 1;
		}else{
			$this->newuser += 1;
			$data['date'] = $this->date;
			$data['hour'] = $this->hour;
			$data['usermac'] = $usermac;
			$data['sys'] = $sys;
			$data['type'] = $type;
			$data['version'] = $version;
			$this->db->AddOne($data);
		}
		$this->AddOrUpdate($sys,$version);

	}

}

//open/pv/android/d4:97:0b:96:c5:91/1.4.6
if ($_SERVER ['QUERY_STRING']) {
	$query =  $_SERVER ['QUERY_STRING'];
	if (stristr($query,'open/pv/')) {
		$queryinfo = explode('/',$query);
		$usermac = isset($queryinfo[3])?$queryinfo[3]:'';

		//mac统一转换成大写
		$usermac = strtoupper($usermac);
		$sys = strtolower($queryinfo[2]);
		$type = $queryinfo[0];
		$version = $queryinfo[4];

		$connect['host'] = 'localhost';
		// $connect['host'] = '192.168.28.201';
		$connect['username'] = 'root';
		$connect['password'] = 'password';
		$connect['dbname'] = 'rht_admin';
		$connect['prefix'] = '';

		$obj = new OpenTrianApp($connect);
		$obj->IntoDatabase($usermac,$sys,$type,$version);
	}

}


