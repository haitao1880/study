<?php 
/**
 * 统计每日每小时的WIFI连接数，每个ap每小时的访问人数
 * 
 */
header("Content-type: text/html; charset=utf-8");
class Wifi_Ap
{
	private $dsn_content = "mysql:host=localhost;dbname=rht_admin";
	//private $dsn_content = "mysql:host=192.168.1.201;dbname=rht_admin";
	private $dbuser_content = 'root';
	private $dbpasswd_content = 'password';
	private $db ;
	
	public function __construct(){
		$this->db = $this->getdb_content();
		$date = date('Y-m-d',strtotime('-1 day'));

		/*$sdate = '2015-01-30';
		$this->execsql($sdate,$date);*/
		$this->BothWifiAp($date);
	}

	//执行
	private function BothWifiAp($date){
		$this->WifiConectNum($date);
		$this->ApQueryNum($date);
	}
	
	//获取当日每个车站每个小时的wifi连接人数
	private function WifiConectNum($date)
	{
		
		$sql_wifi = "SELECT 
						date,HOUR (`time`) AS hour,COUNT(DISTINCT client) AS wifinum,station
					 FROM 
					 	rha_aclog 
					 WHERE 
					 	date = '$date' 
					 GROUP BY 
					 	station,hour 
					 ORDER BY 
					 	null";


		$sql_insert = " INSERT INTO rha_wifi_ap(`date`,`hour`,`wifinum`,`station`) $sql_wifi";
		$this->Execute($sql_insert);
	}

	//获取当日每个车站，每个ap,每个小时的访问人数
	public function ApQueryNum($date)
	{
		$sql_ap = "SELECT
						date,
						HOUR (time) AS hour,
						ap AS apkey,
						COUNT(DISTINCT client) AS apnum,
						station
					FROM
						rha_aclog
					WHERE
						date = '$date'
					GROUP BY
						station,
						apkey,
						hour
					ORDER BY
						null";
		$sql_insert = " INSERT INTO rha_wifi_ap(`date`,`hour`,`apkey`,`apnum`,`station`) $sql_ap";
		$this->Execute($sql_insert);
	}
	/**
	 * 连接数据库
	 * 
	 */
	private function getdb_content(){
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
	private function Execute($sql){
		$dbd = $this->db->prepare($sql);
		$dbd->execute();
	}

	//获取多日数据
	protected function execsql($sdate,$edate){
		$sdate = strtotime($sdate);
		$edate = strtotime($edate);
		while ($sdate <= $edate) {
			$date = date('Y-m-d',$sdate);
			$this->BothWifiAp($date);
			$sdate = strtotime('+1 day',$sdate);
		}
	}
}

$ob = new Wifi_Ap();

?>
