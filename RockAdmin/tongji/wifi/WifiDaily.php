<?php 
/**
 * 统计每日wifi连接数，平均每小时连接数、高峰时段连接数等
 * 
 */
header("Content-type: text/html; charset=utf-8");
class WifiDaily{
	private $dsn_content = "mysql:host=localhost;dbname=rht_admin";
	//private $dsn_content = "mysql:host=192.168.1.201;dbname=rht_admin";
	private $dbuser_content = 'root';
	private $dbpasswd_content = 'password';
	private $db ;
	
	public function __construct(){
		$this->db = $this->getdb_content();
		//$date = date('Y-m-d',strtotime('-1 day'));
		$date = '2015-07-07';
		// $sdate = '2015-01-30';
		// $edate = $date;
		// $this->execsql($sdate,$edate);
		$this->GetConectNumber($date);
	}
	
	
	private function GetConectNumber($date){
		
		$sql_connect = "SELECT
							DAY .date,
							DAY .station,
							DAY .total,
							DAY .avghour,
							top.avgtop,
							maxmax.max
						FROM
							(
								SELECT
									`date`,
									count(DISTINCT `client`) AS `total`,
									`station`,
									round(count(DISTINCT `client`) / 24) AS avghour
								FROM
									`rha_aclog`
								WHERE
									station IN (SELECT id FROM rha_station)
								AND date = '$date'
								GROUP BY
									`station`,
									`date`
							) AS DAY
						LEFT JOIN (
							SELECT
								station,
								sum(avgtop) as avgtop
							FROM
								(
									SELECT
										DATE_FORMAT(
											CONCAT(date, ' ', time),
											'%Y-%m-%d %H'
										) AS sdate,
										station,
										ROUND(count(DISTINCT `client`) / 12) AS `avgtop`
									FROM
										`rha_aclog`
									WHERE
										station IN (SELECT id FROM rha_station)
									AND DATE_FORMAT(
										CONCAT(date, ' ', time),
										'%Y-%m-%d %H'
									) BETWEEN '$date 07'
									AND '$date 19'
									GROUP BY
										station,
										sdate
								) AS t
							GROUP BY
								station
						) AS top ON DAY .station = top.station
						LEFT JOIN (
							SELECT
								station,
								MAX(HOUR) AS max
							FROM
								(
									SELECT
										station,
										count(DISTINCT `client`) AS `hour`,
										DATE_FORMAT(
											CONCAT(date, ' ', time),
											'%Y-%m-%d %H'
										) AS cdate
									FROM
										`rha_aclog`
									WHERE
										station IN (SELECT id FROM rha_station)
									AND DATE_FORMAT(
										CONCAT(date, ' ', time),
										'%Y-%m-%d %H'
									) BETWEEN '$date 00'
									AND '$date 23'
									GROUP BY
										station,
										cdate
								) AS s
							GROUP BY
								station
						) AS maxmax ON DAY .station = maxmax.station";


		$sql_insert = " INSERT INTO rha_wifi_daily(`date`,`station`,`total`,`avghour`,`avgtop`,`maxhour`) $sql_connect";
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
			$this->GetConectNumber($date);
			$sdate = strtotime('+1 day',$sdate);
		}
	}
}

$ob = new WifiDaily();

?>
