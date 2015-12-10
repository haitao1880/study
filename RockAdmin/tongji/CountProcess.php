<?php 
/**
 * 分离流程数据广告1，注册页面，注册数、广告2，首页，下载数，验证码
 * 
 */
header("Content-type: text/html; charset=utf-8");
class CountProcess{

	protected $dsn_content = "mysql:host=localhost;dbname=rht_admin";
	// protected $dsn_content = "mysql:host=192.168.28.201;dbname=rht_admin";
	protected $dbuser_content = 'root';
	protected $dbpasswd_content = 'Cahw_1MLLqIt';
	// protected $dbpasswd_content = 'password';
	protected $db ;
	
	public function __construct(){
		$this->db = $this->getdb_content();
		// $date = '2015-12-04';
		$date = date('Y-m-d',strtotime('-1 day'));
		// $sdate = '2015-08-01';
		// $edate = $date;
		// $this->execsql($sdate,$edate);
		$this->GetConectNumber($date);
		$this->verify($date);
	}

	protected function SetDb($dbname){
		if(!$dbname){
			return;
		}
		$this->dsn_content = "mysql:host=localhost;dbname=$dbname";
		$this->dbuser_content = "root";
		$this->dbpasswd_content = "Cahw_1MLLqIt";
		$this->db = $this->getdb_content();
	}

	//where条件
	protected function getwhere($date){
		$date = str_replace('-','_',$date);
		$ad = " FROM rha_count WHERE date = '$date' AND model = 'ad' AND action = 'visit' AND detail = 'uv'";
		$regpage = " FROM rha_count WHERE date = '$date' AND model = 'register' AND action = 'visit' AND detail = 'uv'";
		$mobileregnum = " FROM rha_count_record WHERE date = '$date' AND model = 'register' AND action = 'login' AND detail = 'success' and did='mobile'";
		$bdfirstregnum = " FROM rha_count_record WHERE date = '$date' AND model = 'register' AND action = 'login' AND detail = 'success' and did='bdfirst'";

		$bdnotfirstregnum = " FROM rha_count_record WHERE date = '$date' AND model = 'register' AND action = 'login' AND detail = 'success' and did='bdnotfirst'";

		$wel = " FROM rha_count_record WHERE date = '$date' AND model = 'welcome' AND action = 'visit' AND detail = 'uv'";
		$sindex = " FROM rha_count_record WHERE date = '$date' AND model = 'sindex' AND action = 'visit' AND detail = 'uv'";
		$dwon = " FROM rha_count_record WHERE date = '$date' AND model in ('game','movie','music','sindex') AND action = 'trainDown' AND detail = 'uv'";
		return array('ad'=>$ad,'regpage'=>$regpage,'mobileregnum'=>$mobileregnum,'bdfirstregnum'=>$bdfirstregnum,'bdnotfirstregnum'=>$bdnotfirstregnum,'wel'=>$wel,'sindex'=>$sindex,'dwon'=>$dwon);
	}

	protected function Set229($dbname){
		if(!$dbname){
			return;
		}
		$this->dsn_content = "mysql:host=118.244.237.229;dbname=$dbname";
		$this->dbuser_content = "terry229";
		$this->dbpasswd_content = "lHye3s2PJs";
		$this->db = $this->getdb_content();
	}

	//插入数据(数据分解)
	protected function GetConectNumber($date){
		$data = $this->getwhere($date);
		foreach($data as $v){
			$sql_connect = "SELECT
							date,
							stationid,
							SUM(dtime) AS dtime,
							model,
							action,
							detail,
							did
						$v
						GROUP BY
							stationid";


			$sql_insert = " INSERT INTO rha_count_process(`date`,`stationid`,`dtime`,`model`,`action`,`detail`,`did`) $sql_connect";
			$this->Execute($sql_insert);
		}
		
	}

	//验证码
	protected function verify($date){
		$this->Set229('rht_idc');
		$sql = "SELECT
				FROM_UNIXTIME(ctime, '%Y_%m_%d') AS date,
				count(*) AS verify
			FROM
				rhi_smsclog
			WHERE
				FROM_UNIXTIME(ctime, '%Y-%m-%d') = '$date'
			AND type = 2 ";
		$res = $this->FetchRow($sql);
		$dates = $res['date'];
		$dtime = $res['verify'];
		$this->SetDb('rht_admin');
		$sql = "INSERT INTO rha_count_process(`date`,`dtime`,`model`,`action`,`detail`) values ('$dates',$dtime,'verify','verify','verify')";
		$this->Execute($sql);
	}

	/**
	 * 返回一行数据
	 * @param 
	 */
	protected function FetchRow($sql)
    {	
		$p = $this->db->query($sql);
		$p->setFetchMode(PDO::FETCH_ASSOC);
		$result = $p->fetchAll();
        return $result[0];
    }
	/**
	 * 连接数据库
	 * 
	 */
	protected function getdb_content(){
		$db = new PDO($this->dsn_content, $this->dbuser_content, $this->dbpasswd_content);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$db->exec('set names utf8');
	
		return $db;
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

	//获取多日数据
	protected function execsql($sdate,$edate){
		$sdate = strtotime($sdate);
		$edate = strtotime($edate);
		while ($sdate <= $edate) {
			
			$date = date('Y-m-d',$sdate);
			// $this->GetConectNumber($date);
			$this->verify($date);
			$sdate = strtotime('+1 day',$sdate);
		}
	}
}

$ob = new CountProcess();

?>
