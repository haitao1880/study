<?php 
/**
 * 根据usermac去掉重复后的aclog数据
 * 
 */
header("Content-type: text/html; charset=utf-8");
class DistinctAclog{

	protected $dsn_content = "mysql:host=localhost;dbname=rht_admin";
	//protected $dsn_content = "mysql:host=192.168.1.201;dbname=rht_admin";
	protected $dbuser_content = 'root';
	protected $dbpasswd_content = 'password';
	protected $db ;
	
	public function __construct(){
		$this->db = $this->getdb_content();
		$date = date('Y-m-d',strtotime('-1 day'));
		$sdate = '2015-03-01';
		$edate = $date;
		$this->execsql($sdate,$edate);
		//$this->GetConectNumber($date);
	}

	protected function SetDb($dbname){
		if(!$dbname){
			return;
		}
		$this->dsn_content = "mysql:host=localhost;dbname=$dbname";
		$this->db = $this->getdb_content();
	}

	

	//插入数据(数据分解)
	protected function GetConectNumber($date){

		if ($date >= '2015-06-04') {
			$table = 'rha_aclog';
		}else{
			$table = 'rha_aclog_bak';
		}		
		$sql_connect = "SELECT
							date,
							station,
							client
						FROM
							$table
						WHERE
							date = '$date'
						AND station != 0
						GROUP BY
							station,
							client";


		$sql_insert = " INSERT INTO rha_distinct_aclog1(`date`,`stationid`,`client`) $sql_connect";
		$this->Execute($sql_insert);
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
}

$ob = new DistinctAclog();

?>
