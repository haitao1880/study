<?php 
/**
 * 统计每日wifi连接数，平均每小时连接数、高峰时段连接数等
 * 
 */
header("Content-type: text/html; charset=utf-8");
class Registerweek{
	private $dsn_content = "mysql:host=localhost;dbname=rht_admin";
	//private $dsn_content = "mysql:host=192.168.28.201;dbname=rht_admin";
	private $dbuser_content = 'root';
	private $dbpasswd_content = 'password';
	private $db ;
	
	public function __construct(){
		$this->db = $this->getdb_content();
		$date = date('Y-m-d',strtotime('-1 day'));
		$edate = $date;
		$sdate = date('Y-m-d',strtotime($edate) - 6*24*3600);
		//$this->execsql($edate);
		$this->GetWeekNumber($sdate,$edate);
	}
	
	
	private function GetWeekNumber($sdate,$edate){
		$sql_reg = "SELECT
						'$sdate' AS sdate,
						'$edate' AS edate,
						sum(dtime) AS totalnum,
						stationid
					FROM
						rha_count_record
					WHERE
						(date BETWEEN '$sdate'
					AND '$edate')
					AND model='register' AND action='login' AND detail = 'success'
					GROUP BY
						stationid";
		
		$sql_insert = " INSERT INTO rha_register_week(`sdate`,`edate`,`totalnum`,`stationid`) $sql_reg";
		$res = $this->Execute($sql_insert);
		if ($res) {
			echo $sdate.'-----'.$edate.'ok'.PHP_EOL;
		}else{
			echo $sdate.'-----'.$edate.'NO'.PHP_EOL;
		}
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
		return $dbd->execute();
	}

	//获取2014-11-10至$edate的数据
	protected function execsql($edate){
		$limitdate = '2014-11-10';
		$limit = strtotime($limitdate);

		$edate = strtotime($edate);
		$sdate = $edate - 6*24*3600;
		$i = 0;
		while ($sdate >= $limit) {
			
			$ssdate = date('Y_m_d',$sdate);
			$eedate = date('Y_m_d',$edate);
			$this->GetWeekNumber($ssdate,$eedate);
			
			if (floor(($sdate/(3600*24) - $limit/(3600*24))) < 7) {
				
				$edate = $sdate - 1;
				$sdate = $limit;
				$i++;
			}else{
				$edate = strtotime('-7 day',$edate);
				$sdate = $edate - 6*24*3600;
			}
			
			if ($i >= 2) {
				break;
			}
		}
	}
}

$ob = new Registerweek();

?>
