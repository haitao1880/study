<?php 
/**
 * 不同日期或者不同站点链接两次以上的人数统计
 * 
 */
header("Content-type: text/html; charset=utf-8");
class WifiMoreConnect{

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
		//$this->count_yue($date);
	}

	protected function SetDb($dbname){
		if(!$dbname){
			return;
		}
		$this->dsn_content = "mysql:host=localhost;dbname=$dbname";
		$this->db = $this->getdb_content();
	}

	

	//插入数据(数据分解)
	protected function count_yue($date){
				
		$sql_connect = "SELECT
							DATE_FORMAT(date, '%Y-%m月') AS yue,
							count(client) AS num
						FROM
							rha_distinct_aclog1
						WHERE
							client IN (
								SELECT
									client
								FROM
									rha_distinct_aclog1
								GROUP BY
									client
								HAVING
									count(client) > 1
							)
						GROUP BY
							yue";


		$sql_insert = " INSERT INTO rha_moreconnect(`date`,`num`) $sql_connect";
		$this->Execute($sql_insert);
	}


	//插入数据(数据分解)
	protected function count_jidu($date){
				
		$sql_connect = "SELECT
							CONCAT(YEAR(date),QUARTER(date),'Q') as jidu,
							count(client) AS num
						FROM
							rha_distinct_aclog1
						WHERE
							client IN (
								SELECT
									client
								FROM
									rha_distinct_aclog1
								GROUP BY
									client
								HAVING
									count(client) > 1
							)
						GROUP BY
							jidu";


		$sql_insert = " INSERT INTO rha_moreconnect(`date`,`num`) $sql_connect";
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
			$this->count_yue($date);
			$sdate = strtotime('+1 day',$sdate);
		}
	}
}

$ob = new WifiMoreConnect();

?>
