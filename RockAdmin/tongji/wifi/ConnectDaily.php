<?php 
/**
 * 从aclog中取出每个站点每天的连接数
 * 
 */
header("Content-type: text/html; charset=utf-8");

class ConnectDaily{
	private $dsn_content = "mysql:host=localhost;dbname=rht_admin";
	private $dbuser_content = 'root';
	private $dbpasswd_content = 'Cahw_1MLLqIt';
	private $db ;
	
	public function __construct(){
		$this->db = $this->getdb_content();
		$date = date('Y-m-d',strtotime('-1 day'));
		// $date = '2015-10-31';
		// $date = '2015-08-22';
		/*$sdate = '2015-07-08';
		$edate = $date;
		$this->execsql($sdate,$edate);*/
		$this->GetConectNumber($date);
	}
	
	
	private function GetConectNumber($date){
		
		$sql_connect = "SELECT `date`, count(DISTINCT `client` ) AS `num`, `station`
			FROM
				`rha_aclog`
			WHERE
				station IN ( SELECT id FROM rha_station ) AND date = ?
			GROUP BY
				`station`,
				`date`";
		$sql_insert = " INSERT INTO rha_aclogview(`date`,`num`,`station`) $sql_connect";
		$this->Execute($sql_insert,array($date));
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
	private function Execute($sql,$data){
		$dbd = $this->db->prepare($sql);
		return $dbd->execute($data);
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


$ob = new ConnectDaily();

?>
