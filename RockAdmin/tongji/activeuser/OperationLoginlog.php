<?php 
/**
 * 分析用户登录记录 * 
 */
header("Content-type: text/html; charset=utf-8");
class Loginlog{
	protected $dsn_content = "mysql:host=localhost;dbname=rht_admin";
	//protected $dsn_content = "mysql:host=192.168.1.201;dbname=rht_admin";
	protected $dbuser_content = 'root';
	protected $dbpasswd_content = 'Cahw_1MLLqIt';
	protected $db ;

	protected $loginlog;
	
	public function __construct(){
		$this->db = $this->getdb_content();		
	}

	/**
	 * 执行
	 */
	public function executeall($date){
		$this->GetLoginlog($date);
		$this->Transformation();
	}



	/**
	 * 获取每天的用户登录记录
	 * @param str $date 日期
	 */
	public function GetLoginlog($date){
		$sql ="SELECT
				username,
				logintime,
				loginip
			FROM
				rha_loginlog
			WHERE
				FROM_UNIXTIME(logintime, '%Y-%m-%d') = '$date'
			AND logintype = 1
			GROUP BY
				loginip,
				username";
		$this->loginlog = $this->GetList($sql);
	}

	/**
	 * 转换数据
	 */
	public function Transformation(){
		foreach($this->loginlog as &$val){
			$val['stationid'] = $this->GetStationid($val['loginip']);
			unset($val['loginip']);

			$this->Insert($val,'rha_login_record');
		}
	}

	/**
	 * 用ip取出stationid 
	 */
	public function GetStationid($ip){
		$ip = long2ip($ip);
		if (strstr($ip,'172.16.')) {
			return 1;
		}

		if (strstr($ip,'20.1.') || strstr($ip,'172.21.')) {
			return 3;
		}

		$ips = explode('.',$ip);
		array_pop($ips);
		$ip = implode($ips,'.');
		$ip .= '.';

		$sql = "SELECT id FROM rha_station WHERE LOCATE('$ip',logip) > 0";		
		$id = $this->FetRow($sql);
		return $id['id'];
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
			$this->GetConectNumber($date);
			$sdate = strtotime('+1 day',$sdate);
		}
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

$m = new Loginlog();
function makedir($m,$sdate,$edate){
	$sdate = strtotime($sdate);
	$edate = strtotime($edate);
	while ($sdate <= $edate) {
		
		$date = date('Y-m-d',$sdate);
		$m -> executeall($date);		
		$sdate = strtotime('+1 day',$sdate);
	}
}

makedir($m,'2015-02-01','2015-11-02');


