<?php 
/**
 * 统计首页游戏广告流程
 * 
 */
header("Content-type: text/html; charset=utf-8");
class Game_sindex_detail_down{
	protected $dsn_content = "mysql:host=localhost;dbname=rht_admin";
	//protected $dsn_content = "mysql:host=192.168.28.201;dbname=rht_admin";
	protected $dbuser_content = 'root';
	protected $dbpasswd_content = 'password';
	protected $db ;
	
	public function __construct(){
		$this->db = $this->getdb_content();
		$date = date('Y_m_d',strtotime('-1 day'));
		$this->getdetail($date);
	}
	
	/**
	 * 筛选数据
	 * @return array     
	 */
	protected function getdetail($date){
		$temptable_sindex = "SELECT sum(dtime) AS num, date, action, detail, sys, stationid FROM rha_count WHERE model = 'sindex' AND locate('game_click', action) > 0 AND date = '$date' GROUP BY stationid, detail, sys";
		$temptable_detail = "SELECT sum(dtime) AS num, date, action, detail, sys, stationid FROM rha_count WHERE model = 'sindex' AND locate('game_detail', action) > 0 AND date = '$date' GROUP BY stationid, detail, sys";

		$temptable_dwon = "SELECT sum(dtime) AS num, date, action, detail, sys, stationid FROM rha_count WHERE model = 'sindex' AND locate('game_down', action) > 0 AND date = '$date' GROUP BY stationid, detail, sys";

		$sql_celect = "SELECT
					sindex.date,
					sindex.detail as appid,
					sindex.num as sindexnum,
					detail.num as detailnum,
					down.num as downnum,
					sindex.sys,
					sindex.stationid
				FROM
					(
						$temptable_sindex
					) AS sindex
				LEFT JOIN(
					$temptable_detail
				) AS detail ON sindex.detail = detail.detail AND sindex.sys=detail.sys AND sindex.stationid=detail.stationid
				LEFT JOIN(
					$temptable_dwon
				) AS down ON detail.detail = down.detail AND detail.sys=down.sys AND detail.stationid=down.stationid";
		$sql = "insert into rha_game_ads_process(`date`,`appid`,`sindexnum`,`detailnum`,`downnum`,`sys`,`stationid`) $sql_celect";
		
		$this->Execute($sql);
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
		return $dbd->execute();
	}

	/**
	 * 执行查询语句
	 */
	protected function query($sql){
		$p = $this->db->query($sql);
		$p->setFetchMode(PDO::FETCH_ASSOC);
		return $p->fetchAll();
	}

}

$ob = new Game_sindex_detail_down();

?>
